<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Stock;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function create()
    {
        $categories = [
            'Rangement et organisation' => '📦 Pour le rangement et l\'organisation',
            'Géométrie et travaux manuels' => '📐 Pour la géométrie et les travaux manuels',
            'Coloriage et surlignage' => '🎨 Pour colorier et surligner',
            'Écriture et correction' => '✍️ Pour écrire et corriger',
            'Lecture et apprentissage' => '📚 Pour lire et apprendre'
        ];
        $unites = ['pièce', 'kg', 'g', 'mètre', 'cm', 'lot', 'boîte', 'paquet'];
        return view('articles.create', compact('categories', 'unites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required|string|max:255|unique:articles',
            'code_barre' => 'nullable|string|max:50|unique:articles',
            'categorie' => 'required|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'unite_mesure' => 'required|string',
            'fournisseur' => 'nullable|string|max:255',
            'emplacement' => 'nullable|string|max:100',
            'poids' => 'nullable|numeric|min:0',
            'marque' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Créer l'article
        $article = Article::create([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'categorie' => $request->categorie,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'prix_unitaire' => $request->prix_vente,
            'benefice' => $request->prix_vente - $request->prix_achat,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
            'unite_mesure' => $request->unite_mesure,
            'fournisseur' => $request->fournisseur,
            'emplacement' => $request->emplacement,
            'poids' => $request->poids,
            'marque' => $request->marque,
            'description' => $request->description,
        ]);

        // Enregistrer dans le stock
        if ($request->stock > 0) {
            Stock::create([
                'article_id' => $article->id,
                'type' => 'entree',
                'quantite' => $request->stock,
                'prix_unitaire' => $request->prix_achat,
                'motif' => 'Création article',
                'date_mouvement' => now(),
                'stock_avant' => 0,
                'stock_apres' => $request->stock,
            ]);
        }

        return redirect()->route('articles.index')->with('success', 'Article créé avec succès !');
    }

    public function show(Article $article)
    {
        $stocks = $article->stocks()->orderBy('created_at', 'desc')->get();
        return view('articles.show', compact('article', 'stocks'));
    }

    public function edit(Article $article)
    {
        $categories = [
            'Rangement et organisation' => '📦 Pour le rangement et l\'organisation',
            'Géométrie et travaux manuels' => '📐 Pour la géométrie et les travaux manuels',
            'Coloriage et surlignage' => '🎨 Pour colorier et surligner',
            'Écriture et correction' => '✍️ Pour écrire et corriger',
            'Lecture et apprentissage' => '📚 Pour lire et apprendre'
        ];
        $unites = ['pièce', 'kg', 'g', 'mètre', 'cm', 'lot', 'boîte', 'paquet'];
        return view('articles.edit', compact('article', 'categories', 'unites'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required|string|max:255|unique:articles,nom_article,' . $article->id,
            'code_barre' => 'nullable|string|max:50|unique:articles,code_barre,' . $article->id,
            'categorie' => 'required|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'unite_mesure' => 'required|string',
            'fournisseur' => 'nullable|string|max:255',
            'emplacement' => 'nullable|string|max:100',
            'poids' => 'nullable|numeric|min:0',
            'marque' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $article->update([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'categorie' => $request->categorie,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'prix_unitaire' => $request->prix_vente,
            'benefice' => $request->prix_vente - $request->prix_achat,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
            'unite_mesure' => $request->unite_mesure,
            'fournisseur' => $request->fournisseur,
            'emplacement' => $request->emplacement,
            'poids' => $request->poids,
            'marque' => $request->marque,
            'description' => $request->description,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès !');
    }

    public function destroy(Article $article)
    {
        if ($article->kits()->count() > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article est utilisé dans des kits.');
        }
        if ($article->stocks()->count() > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article a des mouvements de stock.');
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé !');
    }

    public function exportCSV()
    {
        $articles = Article::all();
        $filename = storage_path('app/temp/articles.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, [
            'ID', 'Code barre', 'Nom', 'Catégorie', "Prix d'achat",
            'Prix de vente', 'Bénéfice', 'Marge (%)', 'Stock',
            'Seuil', 'Unité', 'Fournisseur', 'Emplacement',
            'Poids', 'Marque', 'Description'
        ]);

        foreach ($articles as $article) {
            fputcsv($file, [
                $article->id,
                $article->code_barre,
                $article->nom_article,
                $article->categorie,
                $article->prix_achat,
                $article->prix_vente,
                $article->benefice,
                number_format($article->marge, 2),
                $article->stock,
                $article->seuil_alerte,
                $article->unite_mesure,
                $article->fournisseur ?? '-',
                $article->emplacement ?? '-',
                $article->poids ?? '-',
                $article->marque ?? '-',
                $article->description ?? '-'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'articles-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('fichier');
        $handle = fopen($file->path(), 'r');
        $header = fgetcsv($handle);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            Article::updateOrCreate(
                ['code_barre' => $data['Code barre'] ?? null],
                [
                    'nom_article' => $data['Nom'],
                    'categorie' => $data['Catégorie'],
                    'prix_achat' => $data["Prix d'achat"] ?? 0,
                    'prix_vente' => $data['Prix de vente'] ?? 0,
                    'prix_unitaire' => $data['Prix de vente'] ?? 0,
                    'benefice' => ($data['Prix de vente'] ?? 0) - ($data["Prix d'achat"] ?? 0),
                    'stock' => $data['Stock'] ?? 0,
                    'seuil_alerte' => $data['Seuil'] ?? 5,
                    'unite_mesure' => $data['Unité'] ?? 'pièce',
                    'fournisseur' => $data['Fournisseur'] ?? null,
                    'emplacement' => $data['Emplacement'] ?? null,
                    'poids' => $data['Poids'] ?? null,
                    'marque' => $data['Marque'] ?? null,
                    'description' => $data['Description'] ?? null,
                ]
            );
            $count++;
        }
        fclose($handle);

        return redirect()->route('articles.index')->with('success', $count . ' articles importés avec succès !');
    }
}