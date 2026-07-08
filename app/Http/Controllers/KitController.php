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
        'description' => 'nullable|string',
        'articles' => 'required|array|min:1',
        'articles.*.id' => 'required|exists:articles,id',
        'articles.*.quantite' => 'required|integer|min:1',
    ]);

    // Créer le kit avec les colonnes de base uniquement
    $kit = Kit::create([
        'nom_kit' => $request->nom_kit,
        'description' => $request->description,
        'prix_total' => 0,
    ]);

    // Associer les articles
    foreach ($request->articles as $article) {
        $kit->articles()->attach($article['id'], ['quantite' => $article['quantite']]);
    }

    // Calculer le prix total
    $total = 0;
    foreach ($kit->articles as $article) {
        $total += $article->prix_vente * $article->pivot->quantite;
    }
    $kit->prix_total = $total;
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
            'frais_livraison' => $request->frais_livraison ?? 0,
            'frais_carnet' => $request->frais_carnet ?? 0,
            'frais_emballage' => $request->frais_emballage ?? 0,
            'frais_etiquette' => $request->frais_etiquette ?? 0,
            'en_promotion' => $request->has('en_promotion'),
            'date_debut_promo' => $request->date_debut_promo,
            'date_fin_promo' => $request->date_fin_promo,
            'kit_notes' => $request->kit_notes,
        ]);

        $syncData = [];
        foreach ($request->articles as $article) {
            $syncData[$article['id']] = ['quantite' => $article['quantite']];
        }
        $kit->articles()->sync($syncData);

        $kit->calculerPrixFinal();

        return redirect()->route('kits.index')->with('success', 'Kit modifié avec succès !');
    }

    public function destroy(Kit $kit)
    {
        if ($kit->ventes()->count() > 0) {
            return redirect()->route('kits.index')->with('error', 'Ce kit a des ventes.');
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