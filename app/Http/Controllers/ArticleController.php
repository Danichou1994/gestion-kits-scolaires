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
        return view('articles.index', compact('articles'));
    }

    public function inactifs()
    {
        $articles = Article::where('active', false)->get();
        return view('articles.index', compact('articles'));
    }

    // ⚠️ UNE SEULE MÉTHODE toggleActif AVEC active
    public function toggleActif(Article $article)
    {
        $article->active = !$article->active;
        $article->save();
        $statut = $article->active ? 'activé' : 'désactivé';
        return redirect()->route('articles.index')->with('success', "Article {$statut} avec succès !");
    }

    // ... autres méthodes (create, store, edit, update, destroy, exportCSV, importCSV)
}