<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Article;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with(['article', 'vente'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(50);
        
        $articles = Article::orderBy('nom_article')->get();
        
        return view('stock.index', compact('stocks', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'type_mouvement' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'motif' => 'nullable|string',
        ]);

        $article = Article::find($request->article_id);
        $stockAvant = $article->stock;
        
        if ($request->type_mouvement == 'entree') {
            $article->stock += $request->quantite;
        } else {
            if ($article->stock < $request->quantite) {
                return redirect()->back()->with('error', 'Stock insuffisant !');
            }
            $article->stock -= $request->quantite;
        }
        $article->save();

        Stock::create([
            'article_id' => $request->article_id,
            'type_mouvement' => $request->type_mouvement,
            'quantite' => $request->quantite,
            'prix_unitaire' => $article->prix_achat,
            'motif' => $request->motif,
            'date_mouvement' => now(),
            'stock_avant' => $stockAvant,
            'stock_apres' => $article->stock,
        ]);

        return redirect()->route('stock.index')->with('success', 'Mouvement enregistré !');
    }

    public function historique(Article $article)
{
    $stocks = $article->stocks()->orderBy('created_at', 'desc')->get();
    return view('stock.historique', compact('article', 'stocks'));
}

    public function exportCSV()
    {
        $stocks = Stock::with(['article', 'vente'])->get();
        $filename = storage_path('app/temp/stocks.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['ID', 'Article', 'Type', 'Quantité', 'Prix', 'Avant', 'Après', 'Motif', 'Date']);

        foreach ($stocks as $stock) {
            fputcsv($file, [
                $stock->id,
                $stock->article->nom_article,
                $stock->type_mouvement,
                $stock->quantite,
                $stock->prix_unitaire,
                $stock->stock_avant,
                $stock->stock_apres,
                $stock->motif ?? '-',
                $stock->created_at->format('d/m/Y H:i')
            ]);
        }
        fclose($file);

        return response()->download($filename, 'stocks-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}