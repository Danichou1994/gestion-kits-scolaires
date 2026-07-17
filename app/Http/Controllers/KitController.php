<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitController extends Controller
{
    /**
     * Afficher la liste des kits
     */
    public function index()
    {
        $kits = Kit::with('articles')->get();
        return view('kits.index', compact('kits'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $articles = Article::where('stock', '>', 0)->orderBy('nom_article')->get();
        return view('kits.create', compact('articles'));
    }

    /**
     * Enregistrer un nouveau kit
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_kit' => 'required|unique:kits|max:255',
            'description' => 'nullable|string',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'reduction' => 'nullable|numeric|min:0',
            'frais_livraison' => 'nullable|numeric|min:0',
            'frais_carnet' => 'nullable|numeric|min:0',
            'frais_emballage' => 'nullable|numeric|min:0',
            'frais_etiquette' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcul du prix total
            $prixTotal = 0;
            foreach ($request->articles as $articleData) {
                $article = Article::find($articleData['id']);
                if ($article) {
                    $prixTotal += $article->prix_vente * $articleData['quantite'];
                }
            }

            $reduction = $request->reduction ?? 0;
            $fraisLivraison = $request->frais_livraison ?? 0;
            $fraisCarnet = $request->frais_carnet ?? 0;
            $fraisEmballage = $request->frais_emballage ?? 0;
            $fraisEtiquette = $request->frais_etiquette ?? 0;

            $prixFinal = $prixTotal - $reduction + $fraisLivraison + $fraisCarnet + $fraisEmballage + $fraisEtiquette;

            // Créer le kit
            $kit = Kit::create([
                'nom_kit' => $request->nom_kit,
                'description' => $request->description,
                'prix_total' => $prixTotal,
                'prix_final' => $prixFinal,
                'reduction' => $reduction,
                'frais_livraison' => $fraisLivraison,
                'frais_carnet' => $fraisCarnet,
                'frais_emballage' => $fraisEmballage,
                'frais_etiquette' => $fraisEtiquette,
                'en_promotion' => $request->has('en_promotion'),
                'date_debut_promo' => $request->date_debut_promo,
                'date_fin_promo' => $request->date_fin_promo,
                'kit_notes' => $request->kit_notes,
            ]);

            // Associer les articles
            foreach ($request->articles as $articleData) {
                $kit->articles()->attach($articleData['id'], ['quantite' => $articleData['quantite']]);
            }

            DB::commit();

            return redirect()->route('kits.index')->with('success', 'Kit créé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher un kit
     */
    public function show(Kit $kit)
    {
        $kit->load('articles');
        return view('kits.show', compact('kit'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Kit $kit)
    {
        $articles = Article::where('stock', '>', 0)->orderBy('nom_article')->get();
        $kit->load('articles');
        return view('kits.edit', compact('kit', 'articles'));
    }

    /**
     * Mettre à jour un kit
     */
    public function update(Request $request, Kit $kit)
    {
        $request->validate([
            'nom_kit' => 'required|unique:kits,nom_kit,' . $kit->id . '|max:255',
            'description' => 'nullable|string',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'reduction' => 'nullable|numeric|min:0',
            'frais_livraison' => 'nullable|numeric|min:0',
            'frais_carnet' => 'nullable|numeric|min:0',
            'frais_emballage' => 'nullable|numeric|min:0',
            'frais_etiquette' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcul du prix total
            $prixTotal = 0;
            foreach ($request->articles as $articleData) {
                $article = Article::find($articleData['id']);
                if ($article) {
                    $prixTotal += $article->prix_vente * $articleData['quantite'];
                }
            }

            $reduction = $request->reduction ?? 0;
            $fraisLivraison = $request->frais_livraison ?? 0;
            $fraisCarnet = $request->frais_carnet ?? 0;
            $fraisEmballage = $request->frais_emballage ?? 0;
            $fraisEtiquette = $request->frais_etiquette ?? 0;

            $prixFinal = $prixTotal - $reduction + $fraisLivraison + $fraisCarnet + $fraisEmballage + $fraisEtiquette;

            // Mettre à jour le kit
            $kit->update([
                'nom_kit' => $request->nom_kit,
                'description' => $request->description,
                'prix_total' => $prixTotal,
                'prix_final' => $prixFinal,
                'reduction' => $reduction,
                'frais_livraison' => $fraisLivraison,
                'frais_carnet' => $fraisCarnet,
                'frais_emballage' => $fraisEmballage,
                'frais_etiquette' => $fraisEtiquette,
                'en_promotion' => $request->has('en_promotion'),
                'date_debut_promo' => $request->date_debut_promo,
                'date_fin_promo' => $request->date_fin_promo,
                'kit_notes' => $request->kit_notes,
            ]);

            // Synchroniser les articles
            $syncData = [];
            foreach ($request->articles as $articleData) {
                $syncData[$articleData['id']] = ['quantite' => $articleData['quantite']];
            }
            $kit->articles()->sync($syncData);

            DB::commit();

            return redirect()->route('kits.index')->with('success', 'Kit modifié avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Supprimer un kit
     */
    public function destroy(Kit $kit)
    {
        // Vérifier si le kit est utilisé dans des ventes
        $ventes = \App\Models\Vente::where('items', 'like', '%"id":' . $kit->id . ',"type":"kit"%')->count();
        
        if ($ventes > 0) {
            return redirect()->route('kits.index')->with('error', 'Ce kit est utilisé dans ' . $ventes . ' vente(s) et ne peut pas être supprimé.');
        }

        DB::beginTransaction();

        try {
            $kit->articles()->detach();
            $kit->delete();
            DB::commit();

            return redirect()->route('kits.index')->with('success', 'Kit supprimé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Exporter les kits en CSV
     */
    public function exportCSV()
    {
        $kits = Kit::with('articles')->get();
        $filename = storage_path('app/temp/kits.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['ID', 'Nom', 'Description', 'Total HT', 'Réduction', 'Livraison', 'Carnet', 'Emballage', 'Étiquette', 'Prix final', 'Promotion', 'Articles']);

        foreach ($kits as $kit) {
            $articles = $kit->articles->map(fn($a) => $a->nom_article . ' x' . $a->pivot->quantite)->implode('; ');
            fputcsv($file, [
                $kit->id,
                $kit->nom_kit,
                $kit->description ?? '-',
                $kit->prix_total,
                $kit->reduction,
                $kit->frais_livraison,
                $kit->frais_carnet,
                $kit->frais_emballage,
                $kit->frais_etiquette,
                $kit->prix_final,
                $kit->en_promotion ? 'Oui' : 'Non',
                $articles
            ]);
        }
        fclose($file);

        return response()->download($filename, 'kits-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}