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
        $categories = [
            'Rangement', 'Géométrie', 'Coloriage', 'Écriture', 'Apprentissage'
        ];
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
            'seuil_alerte' => 'required|integer|min:0',
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
            'unite_mesure' => $request->unite_mesure ?? 'pièce',
            'emplacement' => $request->emplacement,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte,
        ]);

        // Ajouter au stock
        Stock::create([
            'article_id' => $article->id,
            'type_mouvement' => 'entree',
            'quantite' => $request->stock,
            'prix_unitaire' => $request->prix_achat,
            'motif' => 'Création article',
            'date_mouvement' => now(),
            'stock_avant' => 0,
            'stock_apres' => $request->stock,
        ]);

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
            'Rangement', 'Géométrie', 'Coloriage', 'Écriture', 'Apprentissage'
        ];
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
            'seuil_alerte' => 'required|integer|min:0',
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
            'unite_mesure' => $request->unite_mesure ?? 'pièce',
            'emplacement' => $request->emplacement,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès !');
    }

    public function destroy(Article $article)
    {
        // Vérifier si l'article est utilisé dans des ventes ou kits
        if ($article->kits()->count() > 0 || $article->stocks()->count() > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article ne peut pas être supprimé car il est utilisé.');
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
        fputcsv($file, ['ID', 'Code barre', 'Nom', 'Catégorie', "Prix d'achat", 'Prix de vente', 'Bénéfice', 'Stock', 'Seuil', 'Fournisseur', 'Emplacement']);
        
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
                $article->fournisseur ?? '-',
                $article->emplacement ?? '-'
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
        
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            
            $article = Article::updateOrCreate(
                ['code_barre' => $data['Code barre'] ?? null],
                [
                    'nom_article' => $data['Nom'],
                    'categorie' => $data['Catégorie'],
                    'prix_achat' => $data["Prix d'achat"],
                    'prix_vente' => $data['Prix de vente'],
                    'prix_unitaire' => $data['Prix de vente'],
                    'benefice' => $data['Prix de vente'] - $data["Prix d'achat"],
                    'stock' => $data['Stock'],
                    'seuil_alerte' => $data['Seuil'] ?? 5,
                    'fournisseur' => $data['Fournisseur'] ?? null,
                    'emplacement' => $data['Emplacement'] ?? null,
                ]
            );
        }
        fclose($handle);

        return redirect()->route('articles.index')->with('success', 'Importation réussie !');
    }
}