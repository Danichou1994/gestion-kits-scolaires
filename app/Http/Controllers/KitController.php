<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KitController extends Controller
{
    /**
     * Afficher la liste des kits
     */
    public function index(Request $request)
    {
        $query = Kit::with('articles');

        // Recherche
        if ($request->filled('search')) {
            $query->rechercher($request->search);
        }

        // Filtre par promotion
        if ($request->filled('promotion')) {
            if ($request->promotion == 'oui') {
                $query->enPromotion();
            } elseif ($request->promotion == 'non') {
                $query->nonEnPromotion();
            }
        }

        // Filtre par prix
        if ($request->filled('prix_min')) {
            $query->prixMinimum($request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->prixMaximum($request->prix_max);
        }

        // Tri
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        $kits = $query->get();

        // Statistiques
        $totalKits = Kit::count();
        $totalKitsEnPromo = Kit::enPromotion()->count();
        $prixMoyen = Kit::avg('prix_final') ?? 0;

        return view('kits.index', compact('kits', 'totalKits', 'totalKitsEnPromo', 'prixMoyen'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $articles = Article::where('stock', '>', 0)
                          ->orderBy('nom_article')
                          ->get();
        return view('kits.create', compact('articles'));
    }

    /**
     * Enregistrer un nouveau kit
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'nom_kit' => 'required|unique:kits|max:255',
            'description' => 'nullable|string|max:1000',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1|max:999',
            'reduction' => 'nullable|numeric|min:0|max:1000000',
            'frais_livraison' => 'nullable|numeric|min:0|max:1000000',
            'frais_carnet' => 'nullable|numeric|min:0|max:1000000',
            'frais_emballage' => 'nullable|numeric|min:0|max:1000000',
            'frais_etiquette' => 'nullable|numeric|min:0|max:1000000',
            'date_debut_promo' => 'nullable|date|after_or_equal:today',
            'date_fin_promo' => 'nullable|date|after:date_debut_promo',
            'kit_notes' => 'nullable|string|max:1000',
        ], [
            'nom_kit.required' => 'Le nom du kit est obligatoire.',
            'nom_kit.unique' => 'Ce nom de kit existe déjà.',
            'articles.required' => 'Veuillez sélectionner au moins un article.',
            'articles.*.id.required' => 'Veuillez sélectionner un article valide.',
            'articles.*.quantite.min' => 'La quantité doit être au moins 1.',
            'date_fin_promo.after' => 'La date de fin doit être après la date de début.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier les stocks
        foreach ($request->articles as $articleData) {
            $article = Article::find($articleData['id']);
            if (!$article) {
                return redirect()->back()
                    ->with('error', 'Article non trouvé.')
                    ->withInput();
            }
            if ($article->stock < $articleData['quantite']) {
                return redirect()->back()
                    ->with('error', 'Stock insuffisant pour "' . $article->nom_article . '". Disponible: ' . $article->stock)
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // Créer le kit
            $kit = Kit::create([
                'nom_kit' => $request->nom_kit,
                'description' => $request->description,
                'reduction' => $request->reduction ?? 0,
                'frais_livraison' => $request->frais_livraison ?? 0,
                'frais_carnet' => $request->frais_carnet ?? 0,
                'frais_emballage' => $request->frais_emballage ?? 0,
                'frais_etiquette' => $request->frais_etiquette ?? 0,
                'en_promotion' => $request->has('en_promotion'),
                'date_debut_promo' => $request->date_debut_promo,
                'date_fin_promo' => $request->date_fin_promo,
                'kit_notes' => $request->kit_notes,
                'prix_total' => 0,
                'prix_final' => 0,
            ]);

            // Associer les articles
            foreach ($request->articles as $articleData) {
                $kit->articles()->attach($articleData['id'], [
                    'quantite' => $articleData['quantite']
                ]);
            }

            // Calculer le prix final
            $kit->calculerPrixFinal();

            DB::commit();

            return redirect()->route('kits.index')
                ->with('success', 'Kit "' . $kit->nom_kit . '" créé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du kit : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher un kit
     */
    public function show(Kit $kit)
    {
        $kit->load('articles');
        
        // Vérifier si le kit est utilisé dans des ventes
        $utiliseDansVentes = \App\Models\Vente::where('items', 'like', '%"id":' . $kit->id . ',"type":"kit"%')->count() > 0;
        
        return view('kits.show', compact('kit', 'utiliseDansVentes'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Kit $kit)
    {
        $articles = Article::where('stock', '>', 0)
                          ->orderBy('nom_article')
                          ->get();
        $kit->load('articles');
        
        // Créer un tableau des articles du kit avec leurs quantités
        $kitArticles = [];
        foreach ($kit->articles as $article) {
            $kitArticles[] = [
                'id' => $article->id,
                'quantite' => $article->pivot->quantite,
                'nom' => $article->nom_article,
                'prix' => $article->prix_vente,
            ];
        }
        
        return view('kits.edit', compact('kit', 'articles', 'kitArticles'));
    }

    /**
     * Mettre à jour un kit
     */
    public function update(Request $request, Kit $kit)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'nom_kit' => 'required|unique:kits,nom_kit,' . $kit->id . '|max:255',
            'description' => 'nullable|string|max:1000',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1|max:999',
            'reduction' => 'nullable|numeric|min:0|max:1000000',
            'frais_livraison' => 'nullable|numeric|min:0|max:1000000',
            'frais_carnet' => 'nullable|numeric|min:0|max:1000000',
            'frais_emballage' => 'nullable|numeric|min:0|max:1000000',
            'frais_etiquette' => 'nullable|numeric|min:0|max:1000000',
            'date_debut_promo' => 'nullable|date',
            'date_fin_promo' => 'nullable|date|after:date_debut_promo',
            'kit_notes' => 'nullable|string|max:1000',
        ], [
            'nom_kit.required' => 'Le nom du kit est obligatoire.',
            'nom_kit.unique' => 'Ce nom de kit existe déjà.',
            'articles.required' => 'Veuillez sélectionner au moins un article.',
            'date_fin_promo.after' => 'La date de fin doit être après la date de début.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le kit
            $kit->update([
                'nom_kit' => $request->nom_kit,
                'description' => $request->description,
                'reduction' => $request->reduction ?? 0,
                'frais_livraison' => $request->frais_livraison ?? 0,
                'frais_carnet' => $request->frais_carnet ?? 0,
                'frais_emballage' => $request->frais_emballage ?? 0,
                'frais_etiquette' => $request->frais_etiquette ?? 0,
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

            // Recalculer le prix final
            $kit->calculerPrixFinal();

            DB::commit();

            return redirect()->route('kits.index')
                ->with('success', 'Kit "' . $kit->nom_kit . '" modifié avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du kit : ' . $e->getMessage())
                ->withInput();
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
            return redirect()->route('kits.index')
                ->with('error', 'Ce kit est utilisé dans ' . $ventes . ' vente(s) et ne peut pas être supprimé.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les relations
            $kit->articles()->detach();
            
            // Supprimer le kit
            $kit->delete();

            DB::commit();

            return redirect()->route('kits.index')
                ->with('success', 'Kit "' . $kit->nom_kit . '" supprimé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du kit : ' . $e->getMessage());
        }
    }

    /**
     * Dupliquer un kit
     */
    public function duplicate(Kit $kit)
    {
        try {
            DB::beginTransaction();

            $nouveauKit = $kit->replicate();
            $nouveauKit->nom_kit = $kit->nom_kit . ' (copie)';
            $nouveauKit->created_at = now();
            $nouveauKit->updated_at = now();
            $nouveauKit->save();

            // Copier les relations
            foreach ($kit->articles as $article) {
                $nouveauKit->articles()->attach($article->id, [
                    'quantite' => $article->pivot->quantite
                ]);
            }

            $nouveauKit->calculerPrixFinal();

            DB::commit();

            return redirect()->route('kits.edit', $nouveauKit)
                ->with('success', 'Kit dupliqué avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la duplication : ' . $e->getMessage());
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

    /**
     * Afficher le rapport des kits
     */
    public function rapport()
    {
        $totalKits = Kit::count();
        $totalKitsEnPromo = Kit::enPromotion()->count();
        $prixMoyen = Kit::avg('prix_final') ?? 0;
        $prixMin = Kit::min('prix_final') ?? 0;
        $prixMax = Kit::max('prix_final') ?? 0;
        $totalReductions = Kit::sum('reduction');
        
        $kitsParMois = Kit::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'), DB::raw('count(*) as total'))
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();

        return view('kits.rapport', compact(
            'totalKits',
            'totalKitsEnPromo',
            'prixMoyen',
            'prixMin',
            'prixMax',
            'totalReductions',
            'kitsParMois'
        ));
    }

    /**
     * Récupérer les articles pour l'API (AJAX)
     */
    public function getArticles(Request $request)
    {
        $search = $request->search ?? '';
        $articles = Article::where('stock', '>', 0)
            ->where('nom_article', 'LIKE', '%' . $search . '%')
            ->limit(10)
            ->get(['id', 'nom_article as nom', 'prix_vente as prix', 'stock']);

        return response()->json($articles);
    }
}