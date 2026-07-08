<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\VenteDetail;
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
        $ventes = Vente::with(['client', 'details'])->orderBy('created_at', 'desc')->get();
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
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:article,kit',
            'items.*.id' => 'required',
            'items.*.quantite' => 'required|integer|min:1',
        ]);

        $montant_ht = 0;
        $details = [];

        foreach ($request->items as $item) {
            if ($item['type'] === 'article') {
                $article = Article::find($item['id']);
                if (!$article || $article->stock < $item['quantite']) {
                    return redirect()->back()->with('error', 'Stock insuffisant pour ' . ($article->nom_article ?? 'article'));
                }
                $prix = $article->prix_vente;
                $total_ligne = $prix * $item['quantite'];
                $details[] = [
                    'type' => 'article',
                    'article_id' => $item['id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $prix,
                    'montant_ht' => $total_ligne,
                    'total_ligne' => $total_ligne,
                ];
                $montant_ht += $total_ligne;
            } else {
                $kit = Kit::find($item['id']);
                if (!$kit) {
                    return redirect()->back()->with('error', 'Kit introuvable');
                }
                $prix = $kit->prix_final;
                $total_ligne = $prix * $item['quantite'];
                $details[] = [
                    'type' => 'kit',
                    'kit_id' => $item['id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $prix,
                    'montant_ht' => $total_ligne,
                    'total_ligne' => $total_ligne,
                ];
                $montant_ht += $total_ligne;
            }
        }

        // Calculs
        $tva = $montant_ht * 0.18;
        $remise = $request->remise ?? 0;
        $frais_livraison = $request->frais_livraison ?? 0;
        $frais_carnet = $request->frais_carnet ?? 0;
        $montant_total = $montant_ht + $tva - $remise + $frais_livraison + $frais_carnet;
        $net_a_payer = $montant_total;

        $numero = Vente::genererNumero();
        $acompte = $montant_total / 4;
        $solde = $montant_total - $acompte;
        $nb_mensualites = $request->nb_mensualites ?? 3;
        $montant_mensualite = $solde / $nb_mensualites;

        $vente = Vente::create([
            'numero_vente' => $numero,
            'client_id' => $request->client_id,
            'type_vente' => 'mixte',
            'montant_ht' => $montant_ht,
            'tva' => $tva,
            'remise' => $remise,
            'frais_livraison' => $frais_livraison,
            'frais_carnet' => $frais_carnet,
            'montant_total' => $montant_total,
            'net_a_payer' => $net_a_payer,
            'acompte' => $acompte,
            'solde' => $solde,
            'nb_mensualites' => $nb_mensualites,
            'montant_mensualite' => $montant_mensualite,
            'statut' => 'en_cours',
            'mode_paiement' => $request->mode_paiement,
            'date_vente' => $request->date_vente ?? now(),
            'notes' => $request->notes,
        ]);

        // Enregistrer les détails
        foreach ($details as $detail) {
            $detail['vente_id'] = $vente->id;
            VenteDetail::create($detail);
        }

        // Gérer le stock
        foreach ($request->items as $item) {
            if ($item['type'] === 'article') {
                $article = Article::find($item['id']);
                $stockAvant = $article->stock;
                $article->stock -= $item['quantite'];
                $article->save();

                Stock::create([
                    'article_id' => $item['id'],
                    'type_mouvement' => 'sortie',
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $article->prix_achat,
                    'vente_id' => $vente->id,
                    'reference' => $numero,
                    'motif' => 'Vente',
                    'stock_avant' => $stockAvant,
                    'stock_apres' => $article->stock,
                    'date_mouvement' => now(),
                ]);
            }
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
        $vente->load(['client', 'details', 'details.article', 'details.kit', 'echeances']);
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

    public function facture(Vente $vente)
    {
        $vente->load(['client', 'details', 'details.article', 'details.kit', 'echeances']);
        $pdf = Pdf::loadView('pdf.facture', compact('vente'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('facture-' . $vente->numero_vente . '.pdf');
    }

    public function facturePreview(Vente $vente)
    {
        $vente->load(['client', 'details', 'details.article', 'details.kit', 'echeances']);
        return view('pdf.facture-preview', compact('vente'));
    }

    public function exportCSV()
    {
        $ventes = Vente::with(['client', 'details'])->get();
        $filename = storage_path('app/temp/ventes.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['N° Vente', 'Date', 'Client', 'Total', 'Acompte', 'Solde', 'Mensualités', 'Statut']);

        foreach ($ventes as $vente) {
            fputcsv($file, [
                $vente->numero_vente,
                $vente->date_vente->format('d/m/Y'),
                $vente->client->nom . ' ' . $vente->client->prenom,
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