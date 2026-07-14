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
        ]);

        $items = json_decode($request->items, true);
        if (empty($items)) {
            return redirect()->back()->with('error', 'Veuillez ajouter au moins un article ou un kit.');
        }

        $sous_total = 0;
        $items_data = [];

        foreach ($items as $item) {
            if ($item['type'] == 'kit') {
                $kit = Kit::find($item['id']);
                if ($kit) {
                    $prix = $kit->prix_final;
                    $total = $prix * $item['quantite'];
                    $sous_total += $total;
                    $items_data[] = [
                        'type' => 'kit',
                        'id' => $kit->id,
                        'nom' => $kit->nom_kit,
                        'quantite' => $item['quantite'],
                        'prix' => $prix,
                        'total' => $total
                    ];
                }
            } else {
                $article = Article::find($item['id']);
                if ($article) {
                    if ($article->stock < $item['quantite']) {
                        return redirect()->back()->with('error', 'Stock insuffisant pour ' . $article->nom_article);
                    }
                    $prix = $article->prix_vente;
                    $total = $prix * $item['quantite'];
                    $sous_total += $total;
                    $items_data[] = [
                        'type' => 'article',
                        'id' => $article->id,
                        'nom' => $article->nom_article,
                        'quantite' => $item['quantite'],
                        'prix' => $prix,
                        'total' => $total
                    ];
                }
            }
        }

        $remise = $request->remise ?? 0;
        $frais_livraison = $request->frais_livraison ?? 0;
        $frais_carnet = $request->frais_carnet ?? 0;

        $montant_final = $sous_total - $remise + $frais_livraison + $frais_carnet;

        // === CALCUL DE LA COMMISSION (10%) ===
        $commission = $montant_final * 0.10;
        $montantNet = $montant_final - $commission;

        $numero = Vente::genererNumero();
        $acompte = $montant_final / 4;
        $solde = $montant_final - $acompte;
        $nb_mensualites = $request->nb_mensualites ?? 3;
        $montant_mensualite = $solde / $nb_mensualites;

        $vente = Vente::create([
            'numero_vente' => $numero,
            'client_id' => $request->client_id,
            'type_vente' => 'mixte',
            'items' => json_encode($items_data),
            'sous_total' => $sous_total,
            'montant_total' => $montant_final,
            'commission' => $commission,        // AJOUTÉ
            'montant_net' => $montantNet,       // AJOUTÉ
            'remise' => $remise,
            'frais_livraison' => $frais_livraison,
            'frais_carnet' => $frais_carnet,
            'acompte' => $acompte,
            'solde' => $solde,
            'nb_mensualites' => $nb_mensualites,
            'montant_mensualite' => $montant_mensualite,
            'statut' => 'en_cours',
            'mode_paiement' => $request->mode_paiement,
            'date_vente' => $request->date_vente ?? now(),
            'notes' => $request->notes,
        ]);

        // Mettre à jour les stocks
        foreach ($items_data as $item) {
            if ($item['type'] == 'article') {
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
        $vente->load(['client', 'kit', 'kit.articles', 'article', 'echeances']);
        
        // Calculs pour la facture
        $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_du');
        $totalRestant = $vente->solde - $totalPaye;
        
        $prochaineEcheance = $vente->echeances()
            ->where('statut', 'en_attente')
            ->orderBy('date_echeance')
            ->first();
        
        return view('ventes.show', compact('vente', 'totalPaye', 'totalRestant', 'prochaineEcheance'));
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
        $vente->echeances()->delete();
        $vente->delete();
        return redirect()->route('ventes.index')->with('success', 'Vente supprimée avec succès !');
    }

    public function facture(Vente $vente)
    {
        $vente->load(['client', 'kit.articles', 'article', 'echeances']);
        $pdf = Pdf::loadView('pdf.facture', compact('vente'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('facture-' . $vente->numero_vente . '.pdf');
    }

    public function facturePreview(Vente $vente)
    {
        $vente->load(['client', 'kit.articles', 'article', 'echeances']);
        return view('pdf.facture-preview', compact('vente'));
    }

    public function exportCSV()
    {
        $ventes = Vente::with(['client', 'kit', 'article'])->get();
        $filename = storage_path('app/temp/ventes.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['N° Vente', 'Date', 'Client', 'Type', 'Produit', 'Qté', 'Total', 'Commission', 'Net', 'Acompte', 'Solde', 'Mensualités', 'Statut']);

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
                $vente->commission ?? 0,
                $vente->montant_net ?? 0,
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