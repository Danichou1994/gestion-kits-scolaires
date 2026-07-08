<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Kit;
use App\Models\Article;
use App\Models\Echeance;
use App\Models\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with(['client', 'kit', 'article'])->orderBy('created_at', 'desc')->get();
        $totalVentes = Vente::count();
        $totalChiffre = Vente::sum('montant_total');
        $totalSolde = Vente::sum('solde');
        $ventesEnCours = Vente::where('statut', 'en_cours')->count();
        
        return view('ventes.index', compact('ventes', 'totalVentes', 'totalChiffre', 'totalSolde', 'ventesEnCours'));
    }

    public function create()
    {
        $clients = Client::all();
        $kits = Kit::all();
        $articles = Article::where('stock', '>', 0)->get();
        return view('ventes.create', compact('clients', 'kits', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type_vente' => 'required|in:article,kit',
        ]);

        if ($request->type_vente === 'kit') {
            $request->validate(['kit_id' => 'required|exists:kits,id']);
            $kit = Kit::find($request->kit_id);
            $montant_total = $kit->prix_final;
            $type_label = 'kit';
            $item_id = $request->kit_id;
        } else {
            $request->validate([
                'article_id' => 'required|exists:articles,id',
                'quantite' => 'required|integer|min:1',
            ]);
            $article = Article::find($request->article_id);
            if ($article->stock < $request->quantite) {
                return redirect()->back()->with('error', 'Stock insuffisant ! (Stock: ' . $article->stock . ')');
            }
            $montant_total = $article->prix_vente * $request->quantite;
            $type_label = 'article';
            $item_id = $request->article_id;
        }

        $numero = Vente::genererNumero();
        $acompte = $montant_total / 4;
        $solde = $montant_total - $acompte;
        $nb_mensualites = $request->nb_mensualites ?? 3;
        $montant_mensualite = $solde / $nb_mensualites;

        $vente = Vente::create([
            'numero_vente' => $numero,
            'client_id' => $request->client_id,
            'type_vente' => $type_label,
            'kit_id' => $request->kit_id ?? null,
            'article_id' => $request->article_id ?? null,
            'quantite' => $request->quantite ?? 1,
            'montant_total' => $montant_total,
            'remise' => $request->remise ?? 0,
            'frais_livraison' => $request->frais_livraison ?? 0,
            'frais_carnet' => $request->frais_carnet ?? 0,
            'acompte' => $acompte,
            'solde' => $solde,
            'nb_mensualites' => $nb_mensualites,
            'montant_mensualite' => $montant_mensualite,
            'statut' => 'en_cours',
            'mode_paiement' => $request->mode_paiement,
            'date_vente' => $request->date_vente ?? now(),
            'notes' => $request->notes,
        ]);

        // Stock sortie si vente article
        if ($request->type_vente === 'article') {
            $article = Article::find($request->article_id);
            $stockAvant = $article->stock;
            $article->stock -= $request->quantite;
            $article->save();

            Stock::create([
                'article_id' => $request->article_id,
                'type_mouvement' => 'sortie',
                'quantite' => $request->quantite,
                'prix_unitaire' => $article->prix_achat,
                'vente_id' => $vente->id,
                'reference' => $numero,
                'motif' => 'Vente',
                'stock_avant' => $stockAvant,
                'stock_apres' => $article->stock,
                'date_mouvement' => now(),
            ]);
        }

        // Générer les échéances
        $date_echeance = Carbon::parse($vente->date_vente);
        for ($i = 1; $i <= $nb_mensualites; $i++) {
            $date_echeance->addMonth();
            Echeance::create([
                'vente_id' => $vente->id,
                'client_id' => $request->client_id,
                'date_echeance' => $date_echeance->copy(),
                'montant_dû' => $montant_mensualite,
                'statut' => 'en_attente'
            ]);
        }

        return redirect()->route('ventes.index')->with('success', 'Vente #' . $numero . ' enregistrée avec succès !');
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'kit', 'kit.articles', 'article', 'echeances']);
        return view('ventes.show', compact('vente'));
    }

    public function edit(Vente $vente)
    {
        $clients = Client::all();
        $kits = Kit::all();
        $articles = Article::where('stock', '>', 0)->get();
        return view('ventes.edit', compact('vente', 'clients', 'kits', 'articles'));
    }

    public function update(Request $request, Vente $vente)
    {
        $vente->update($request->all());
        return redirect()->route('ventes.index')->with('success', 'Vente modifiée !');
    }

    public function destroy(Vente $vente)
    {
        if ($vente->echeances()->where('statut', '!=', 'paye')->count() > 0) {
            return redirect()->back()->with('error', 'Cette vente a des échéances non payées.');
        }
        $vente->echeances()->delete();
        $vente->delete();
        return redirect()->route('ventes.index')->with('success', 'Vente supprimée !');
    }

    // ====== FACTURE PDF ======
    public function facture(Vente $vente)
    {
        $vente->load(['client', 'kit.articles', 'article', 'echeances']);
        $pdf = Pdf::loadView('pdf.facture', compact('vente'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('facture-' . $vente->numero_vente . '.pdf');
    }

    // ====== FACTURE AVEC APERÇU ======
    public function facturePreview(Vente $vente)
    {
        $vente->load(['client', 'kit.articles', 'article', 'echeances']);
        return view('pdf.facture-preview', compact('vente'));
    }

    // ====== EXPORT CSV ======
    public function exportCSV()
    {
        $ventes = Vente::with(['client', 'kit', 'article'])->get();
        $filename = storage_path('app/temp/ventes.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['N° Vente', 'Date', 'Client', 'Type', 'Article/Kit', 'Qté', 'Total', 'Acompte', 'Solde', 'Mensualités', 'Statut']);

        foreach ($ventes as $vente) {
            $item = $vente->type_vente == 'kit' ? $vente->kit->nom_kit ?? 'N/A' : $vente->article->nom_article ?? 'N/A';
            fputcsv($file, [
                $vente->numero_vente,
                $vente->date_vente->format('d/m/Y'),
                $vente->client->nom . ' ' . $vente->client->prenom,
                $vente->type_vente == 'kit' ? 'Kit' : 'Article',
                $item,
                $vente->quantite ?? 1,
                $vente->montant_total,
                $vente->acompte,
                $vente->solde,
                $vente->nb_mensualites,
                $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé')
            ]);
        }
        fclose($file);

        return response()->download($filename, 'ventes-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}