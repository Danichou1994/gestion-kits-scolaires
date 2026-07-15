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
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles',
            'prix_achat' => 'required|numeric',
            'prix_vente' => 'required|numeric',
            'stock' => 'required|integer',
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
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles,nom_article,' . $article->id,
            'prix_achat' => 'required|numeric',
            'prix_vente' => 'required|numeric',
            'stock' => 'required|integer',
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

    public function exportCSV()
    {
        $articles = Article::all();
        $filename = storage_path('app/temp/articles.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['ID', 'Nom', 'Code barre', 'Prix achat', 'Prix vente', 'Bénéfice', 'Catégorie', 'Fournisseur', 'Stock', 'Seuil alerte', 'Actif']);

        foreach ($articles as $article) {
            fputcsv($file, [
                $article->id,
                $article->nom_article,
                $article->code_barre,
                $article->prix_achat,
                $article->prix_vente,
                $article->benefice,
                $article->categorie,
                $article->fournisseur,
                $article->stock,
                $article->seuil_alerte,
                $article->active ? 'Oui' : 'Non'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'articles-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            Article::create([
                'nom_article' => $data['Nom'],
                'code_barre' => $data['Code barre'] ?? null,
                'prix_achat' => $data['Prix achat'],
                'prix_vente' => $data['Prix vente'],
                'benefice' => $data['Prix vente'] - $data['Prix achat'],
                'categorie' => $data['Catégorie'],
                'fournisseur' => $data['Fournisseur'] ?? null,
                'stock' => $data['Stock'],
                'seuil_alerte' => $data['Seuil alerte'] ?? 5,
                'active' => true,
            ]);
        }
        fclose($handle);

        return redirect()->route('articles.index')->with('success', 'Articles importés avec succès !');
    }
}