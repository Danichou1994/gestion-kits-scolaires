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
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock'));
    }

    public function create()
    {
        $categories = ['Rangement', 'Géométrie', 'Coloriage', 'Écriture', 'Apprentissage'];
        return view('articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles',
            'code_barre' => 'nullable|unique:articles',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'categorie' => 'required',
            'stock' => 'required|integer|min:0',
        ]);

        $article = Article::create([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'prix_unitaire' => $request->prix_vente,
            'benefice' => $request->prix_vente - $request->prix_achat,
            'categorie' => $request->categorie,
            'fournisseur' => $request->fournisseur,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
        ]);

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

        return redirect()->route('articles.index')->with('success', 'Article créé !');
    }

    public function show(Article $article)
    {
        $stocks = $article->stocks()->orderBy('created_at', 'desc')->get();
        return view('articles.show', compact('article', 'stocks'));
    }

    public function edit(Article $article)
    {
        $categories = ['Rangement', 'Géométrie', 'Coloriage', 'Écriture', 'Apprentissage'];
        return view('articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'categorie' => 'required',
            'stock' => 'required|integer|min:0',
        ]);

        $article->update([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'prix_unitaire' => $request->prix_vente,
            'benefice' => $request->prix_vente - $request->prix_achat,
            'categorie' => $request->categorie,
            'fournisseur' => $request->fournisseur,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article modifié !');
    }

    public function destroy(Article $article)
    {
        if ($article->kits()->count() > 0 || $article->stocks()->count() > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article est utilisé.');
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
        fputcsv($file, ['ID', 'Code', 'Nom', 'Catégorie', "Prix d'achat", 'Prix de vente', 'Bénéfice', 'Stock', 'Seuil', 'Fournisseur']);

        foreach ($articles as $article) {
            fputcsv($file, [
                $article->id,
                $article->code_barre,
                $article->nom_article,
                $article->categorie,
                $article->prix_achat,
                $article->prix_vente,
                $article->benefice,
                $article->stock,
                $article->seuil_alerte,
                $article->fournisseur ?? '-'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'articles-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}