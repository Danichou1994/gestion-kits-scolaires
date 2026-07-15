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
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with(['client'])->orderBy('created_at', 'desc')->get();
        $totalVentes = Vente::count();
        $totalChiffre = Vente::sum('montant_total');
        $totalSolde = Vente::sum('solde');
        $ventesEnCours = Vente::where('statut', 'en_cours')->count();
        
        return view('ventes.index', compact('ventes', 'totalVentes', 'totalChiffre', 'totalSolde', 'ventesEnCours'));
    }

    public function create()
    {
        $clients = Client::all();
        $kits = Kit::with('articles')->get();
        $articles = Article::where('stock', '>', 0)->get();
        return view('ventes.create', compact('clients', 'kits', 'articles'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|json',
            'nb_mensualites' => 'required|integer|min:1|max:12',
        ]);

        $items = json_decode($request->items, true);
        if (empty($items)) {
            return redirect()->back()->with('error', 'Veuillez ajouter au moins un article ou un kit.');
        }

        DB::beginTransaction();

        try {
            $sous_total = 0;
            $items_data = [];

            foreach ($items as $item) {
                if ($item['type'] == 'kit') {
                    $kit = Kit::with('articles')->find($item['id']);
                    if ($kit) {
                        // Calculer le prix du kit à partir de ses articles
                        $prixKit = 0;
                        foreach ($kit->articles as $article) {
                            $prixKit += $article->prix_vente * $article->pivot->quantite;
                        }
                        // Ajouter les frais du kit
                        $prixKit = $prixKit - $kit->reduction + $kit->frais_livraison + $kit->frais_carnet + $kit->frais_emballage + $kit->frais_etiquette;
                        
                        $total = $prixKit * $item['quantite'];
                        $sous_total += $total;
                        $items_data[] = [
                            'type' => 'kit',
                            'id' => $kit->id,
                            'nom' => $kit->nom_kit,
                            'quantite' => $item['quantite'],
                            'prix' => $prixKit,
                            'total' => $total
                        ];
                    }
                } else {
                    $article = Article::find($item['id']);
                    if ($article) {
                        if ($article->stock < $item['quantite']) {
                            DB::rollBack();
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

            // Calcul de la commission (10%)
            $commission = $montant_final * 0.10;
            $montantNet = $montant_final - $commission;

            $numero = Vente::genererNumero();
            $acompte = $montant_final / 4;
            $solde = $montant_final - $acompte;
            $nb_mensualites = $request->nb_mensualites ?? 3;
            $montant_mensualite = $solde / $nb_mensualites;

            // Créer la vente
            $vente = Vente::create([
                'numero_vente' => $numero,
                'client_id' => $request->client_id,
                'type_vente' => 'mixte',
                'items' => json_encode($items_data),
                'sous_total' => $sous_total,
                'montant_total' => $montant_final,
                'commission' => $commission,
                'montant_net' => $montantNet,
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
                    if ($article) {
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

            DB::commit();

            return redirect()->route('ventes.index')->with('success', 'Vente #' . $numero . ' enregistrée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la création de la vente : ' . $e->getMessage());
        }
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'echeances']);
        
        // Récupérer les items de la vente
        $items = json_decode($vente->items, true) ?? [];
        
        // Calcul du total payé
        $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_dû');
        $totalRestant = $vente->solde - $totalPaye;
        
        $prochaineEcheance = $vente->echeances()
            ->where('statut', 'en_attente')
            ->orderBy('date_echeance')
            ->first();
        
        return view('ventes.show', compact('vente', 'items', 'totalPaye', 'totalRestant', 'prochaineEcheance'));
    }

    public function edit(Vente $vente)
    {
        $clients = Client::all();
        $kits = Kit::all();
        $articles = Article::where('stock', '>', 0)->get();
        $items = json_decode($vente->items, true) ?? [];
        
        return view('ventes.edit', compact('vente', 'clients', 'kits', 'articles', 'items'));
    }

    public function update(Request $request, Vente $vente)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|json',
            'nb_mensualites' => 'required|integer|min:1|max:12',
        ]);

        $items = json_decode($request->items, true);
        if (empty($items)) {
            return redirect()->back()->with('error', 'Veuillez ajouter au moins un article ou un kit.');
        }

        DB::beginTransaction();

        try {
            // Récupérer les anciens items pour restaurer les stocks
            $oldItems = json_decode($vente->items, true) ?? [];
            
            // Restaurer les stocks des anciens articles
            foreach ($oldItems as $item) {
                if ($item['type'] == 'article') {
                    $article = Article::find($item['id']);
                    if ($article) {
                        $article->stock += $item['quantite'];
                        $article->save();
                    }
                }
            }

            $sous_total = 0;
            $items_data = [];

            foreach ($items as $item) {
                if ($item['type'] == 'kit') {
                    $kit = Kit::with('articles')->find($item['id']);
                    if ($kit) {
                        $prixKit = 0;
                        foreach ($kit->articles as $article) {
                            $prixKit += $article->prix_vente * $article->pivot->quantite;
                        }
                        $prixKit = $prixKit - $kit->reduction + $kit->frais_livraison + $kit->frais_carnet + $kit->frais_emballage + $kit->frais_etiquette;
                        
                        $total = $prixKit * $item['quantite'];
                        $sous_total += $total;
                        $items_data[] = [
                            'type' => 'kit',
                            'id' => $kit->id,
                            'nom' => $kit->nom_kit,
                            'quantite' => $item['quantite'],
                            'prix' => $prixKit,
                            'total' => $total
                        ];
                    }
                } else {
                    $article = Article::find($item['id']);
                    if ($article) {
                        if ($article->stock < $item['quantite']) {
                            DB::rollBack();
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

            // Calcul de la commission (10%)
            $commission = $montant_final * 0.10;
            $montantNet = $montant_final - $commission;

            $acompte = $montant_final / 4;
            $solde = $montant_final - $acompte;
            $nb_mensualites = $request->nb_mensualites ?? 3;
            $montant_mensualite = $solde / $nb_mensualites;

            // Mettre à jour la vente
            $vente->update([
                'client_id' => $request->client_id,
                'items' => json_encode($items_data),
                'sous_total' => $sous_total,
                'montant_total' => $montant_final,
                'commission' => $commission,
                'montant_net' => $montantNet,
                'remise' => $remise,
                'frais_livraison' => $frais_livraison,
                'frais_carnet' => $frais_carnet,
                'acompte' => $acompte,
                'solde' => $solde,
                'nb_mensualites' => $nb_mensualites,
                'montant_mensualite' => $montant_mensualite,
                'mode_paiement' => $request->mode_paiement,
                'date_vente' => $request->date_vente ?? now(),
                'notes' => $request->notes,
            ]);

            // Mettre à jour les stocks des nouveaux articles
            foreach ($items_data as $item) {
                if ($item['type'] == 'article') {
                    $article = Article::find($item['id']);
                    if ($article) {
                        $stockAvant = $article->stock;
                        $article->stock -= $item['quantite'];
                        $article->save();

                        Stock::create([
                            'article_id' => $item['id'],
                            'type_mouvement' => 'sortie',
                            'quantite' => $item['quantite'],
                            'prix_unitaire' => $article->prix_achat,
                            'vente_id' => $vente->id,
                            'reference' => $vente->numero_vente,
                            'motif' => 'Modification vente',
                            'stock_avant' => $stockAvant,
                            'stock_apres' => $article->stock,
                            'date_mouvement' => now(),
                        ]);
                    }
                }
            }

            // Supprimer les anciennes échéances
            $vente->echeances()->delete();

            // Générer les nouvelles échéances
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

            DB::commit();

            return redirect()->route('ventes.index')->with('success', 'Vente #' . $vente->numero_vente . ' modifiée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la modification de la vente : ' . $e->getMessage());
        }
    }

    public function destroy(Vente $vente)
    {
        DB::beginTransaction();

        try {
            // Restaurer les stocks
            $items = json_decode($vente->items, true) ?? [];
            foreach ($items as $item) {
                if ($item['type'] == 'article') {
                    $article = Article::find($item['id']);
                    if ($article) {
                        $article->stock += $item['quantite'];
                        $article->save();
                    }
                }
            }

            // Supprimer les échéances
            $vente->echeances()->delete();
            
            // Supprimer les stocks associés
            Stock::where('vente_id', $vente->id)->delete();
            
            // Supprimer la vente
            $vente->delete();

            DB::commit();

            return redirect()->route('ventes.index')->with('success', 'Vente supprimée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function facture(Vente $vente)
{
    $vente->load(['client', 'echeances']);
    $items = json_decode($vente->items, true) ?? [];
    $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_dû');
    $totalRestant = $vente->solde - $totalPaye;
    
    $pdf = Pdf::loadView('pdf.facture', compact('vente', 'items', 'totalPaye', 'totalRestant'));
    $pdf->setPaper('a4', 'portrait');
    
    // 🔥 NOM DU FICHIER : facture_PRENOM_NOM_NUMERO.pdf
    $nomClient = $vente->client->prenom . '_' . $vente->client->nom;
    $nomFichier = 'facture_' . $nomClient . '_' . $vente->numero_vente . '.pdf';
    
    return $pdf->download($nomFichier);
}

    public function facturePreview(Vente $vente)
    {
        $vente->load(['client', 'echeances']);
        $items = json_decode($vente->items, true) ?? [];
        $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_dû');
        $totalRestant = $vente->solde - $totalPaye;
        
        return view('pdf.facture-preview', compact('vente', 'items', 'totalPaye', 'totalRestant'));
    }

    public function exportCSV()
    {
        $ventes = Vente::with(['client'])->get();
        $filename = storage_path('app/temp/ventes.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['N° Vente', 'Date', 'Client', 'Total', 'Commission', 'Net', 'Acompte', 'Solde', 'Mensualités', 'Statut']);

        foreach ($ventes as $vente) {
            fputcsv($file, [
                $vente->numero_vente,
                $vente->date_vente->format('d/m/Y'),
                $vente->client->nom . ' ' . $vente->client->prenom,
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