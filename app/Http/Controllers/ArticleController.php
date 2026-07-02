<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required',
            'prix_unitaire' => 'required|numeric|min:0',
            'categorie' => 'required',
            'stock' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0'
        ]);

        Article::create($request->all());
        return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès !');
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required',
            'prix_unitaire' => 'required|numeric|min:0',
            'categorie' => 'required',
            'stock' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0'
        ]);

        $article->update($request->all());
        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès !');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé !');
    }
}