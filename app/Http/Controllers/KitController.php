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
            'nom_kit' => 'required|unique:kits',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
        ]);

        $kit = Kit::create([
            'nom_kit' => $request->nom_kit,
            'description' => $request->description,
            'reduction' => $request->reduction ?? 0,
            'prix_livraison' => $request->prix_livraison ?? 0,
            'prix_carnet' => $request->prix_carnet ?? 0,
        ]);

        foreach ($request->articles as $article) {
            $kit->articles()->attach($article['id'], ['quantite' => $article['quantite']]);
        }

        $kit->calculerPrixFinal();
        $kit->save();

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
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
        ]);

        $kit->update([
            'nom_kit' => $request->nom_kit,
            'description' => $request->description,
            'reduction' => $request->reduction ?? 0,
            'prix_livraison' => $request->prix_livraison ?? 0,
            'prix_carnet' => $request->prix_carnet ?? 0,
        ]);

        $syncData = [];
        foreach ($request->articles as $article) {
            $syncData[$article['id']] = ['quantite' => $article['quantite']];
        }
        $kit->articles()->sync($syncData);

        $kit->calculerPrixFinal();
        $kit->save();

        return redirect()->route('kits.index')->with('success', 'Kit modifié avec succès !');
    }

    public function destroy(Kit $kit)
    {
        if ($kit->ventes()->count() > 0) {
            return redirect()->route('kits.index')->with('error', 'Ce kit ne peut pas être supprimé car il a des ventes.');
        }
        $kit->articles()->detach();
        $kit->delete();
        return redirect()->route('kits.index')->with('success', 'Kit supprimé !');
    }

    public function exportCSV()
    {
        $kits = Kit::with('articles')->get();
        $filename = storage_path('app/temp/kits.csv');
        
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
        
        $file = fopen($filename, 'w');
        fputcsv($file, ['ID', 'Nom', 'Description', 'Prix total', 'Réduction', 'Livraison', 'Carnet', 'Prix final', 'Articles']);
        
        foreach ($kits as $kit) {
            $articlesList = $kit->articles->map(function($article) {
                return $article->nom_article . ' (x' . $article->pivot->quantite . ')';
            })->implode('; ');
            
            fputcsv($file, [
                $kit->id,
                $kit->nom_kit,
                $kit->description ?? '-',
                $kit->prix_total,
                $kit->reduction,
                $kit->prix_livraison,
                $kit->prix_carnet,
                $kit->prix_final,
                $articlesList
            ]);
        }
        fclose($file);
        
        return response()->download($filename, 'kits-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}