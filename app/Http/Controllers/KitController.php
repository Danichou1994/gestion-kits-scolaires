<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use App\Models\Article;
use Illuminate\Http\Request;

class KitController extends Controller
{
    public function index()
    {
        $kits = Kit::with('articles')->get();
        return view('kits.index', compact('kits'));
    }

    public function create()
    {
        $articles = Article::all();
        return view('kits.create', compact('articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_kit' => 'required',
            'description' => 'nullable',
            'prix_total' => 'required|numeric|min:0',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1'
        ]);

        $kit = Kit::create([
            'nom_kit' => $request->nom_kit,
            'description' => $request->description,
            'prix_total' => $request->prix_total
        ]);

        // Associer les articles au kit
        foreach ($request->articles as $article) {
            $kit->articles()->attach($article['id'], ['quantite' => $article['quantite']]);
        }

        return redirect()->route('kits.index')->with('success', 'Kit créé avec succès !');
    }

    public function show(Kit $kit)
    {
        $kit->load('articles');
        return view('kits.show', compact('kit'));
    }

    public function edit(Kit $kit)
    {
        $articles = Article::all();
        $kit->load('articles');
        return view('kits.edit', compact('kit', 'articles'));
    }

    public function update(Request $request, Kit $kit)
    {
        $request->validate([
            'nom_kit' => 'required',
            'description' => 'nullable',
            'prix_total' => 'required|numeric|min:0',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1'
        ]);

        $kit->update([
            'nom_kit' => $request->nom_kit,
            'description' => $request->description,
            'prix_total' => $request->prix_total
        ]);

        // Synchroniser les articles
        $syncData = [];
        foreach ($request->articles as $article) {
            $syncData[$article['id']] = ['quantite' => $article['quantite']];
        }
        $kit->articles()->sync($syncData);

        return redirect()->route('kits.index')->with('success', 'Kit modifié avec succès !');
    }

    public function destroy(Kit $kit)
    {
        $kit->articles()->detach();
        $kit->delete();
        return redirect()->route('kits.index')->with('success', 'Kit supprimé !');
    }
}