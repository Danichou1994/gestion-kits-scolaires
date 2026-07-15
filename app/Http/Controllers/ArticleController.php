<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('active', 'desc')->get();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function actifs()
    {
        $articles = Article::where('active', true)->get();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function inactifs()
    {
        $articles = Article::where('active', false)->get();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function toggleActif(Article $article)
    {
        $article->active = !$article->active;
        $article->save();
        $statut = $article->active ? 'activé' : 'désactivé';
        return redirect()->route('articles.index')->with('success', "Article {$statut} avec succès !");
    }

    public function create()
    {
        $categories = [
            'Rangement et organisation' => 'Rangement et organisation',
            'Géométrie et travaux manuels' => 'Géométrie et travaux manuels',
            'Coloriage et surlignage' => 'Coloriage et surlignage',
            'Écriture et correction' => 'Écriture et correction',
            'Lecture et apprentissage' => 'Lecture et apprentissage'
        ];
        return view('articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categorie' => 'required',
        ]);

        $benefice = $request->prix_vente - $request->prix_achat;

        Article::create([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'benefice' => $benefice,
            'categorie' => $request->categorie,
            'fournisseur' => $request->fournisseur,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
            'active' => true,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès !');
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $categories = [
            'Rangement et organisation' => 'Rangement et organisation',
            'Géométrie et travaux manuels' => 'Géométrie et travaux manuels',
            'Coloriage et surlignage' => 'Coloriage et surlignage',
            'Écriture et correction' => 'Écriture et correction',
            'Lecture et apprentissage' => 'Lecture et apprentissage'
        ];
        return view('articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles,nom_article,' . $article->id,
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categorie' => 'required',
        ]);

        $benefice = $request->prix_vente - $request->prix_achat;

        $article->update([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'benefice' => $benefice,
            'categorie' => $request->categorie,
            'fournisseur' => $request->fournisseur,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès !');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès !');
    }
}