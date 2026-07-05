<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Kit;
use App\Models\Article;
use App\Models\Echeance;
use App\Models\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with(['client', 'kit', 'article'])->orderBy('created_at', 'desc')->get();
        return view('ventes.index', compact('ventes'));
    }

    public function create()
    {
        $clients = Client::all();
        $kits = Kit::all();
        $articles = Article::where('stock', '>', 0)->get();
        return view('ventes.create', compact('clients', 'kits', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type_vente' => 'required|in:article,kit',
        ]);

        if ($request->type_vente == 'kit') {
            $request->validate([
                'kit_id' => 'required|exists:kits,id',
            ]);
            $kit = Kit::find($request->kit_id);
            $montant_total = $kit->prix_final;
        } else {
            $request->validate([
                'article_id' => 'required|exists:articles,id',
                'quantite' => 'required|integer|min:1',
            ]);
            $article = Article::find($request->article_id);
            if ($article->stock < $request->quantite) {
                return redirect()->back()->with('error', 'Stock insuffisant !');
            }
            $montant_total = $article->prix_vente * $request->quantite;
        }

        $numero = Vente::genererNumero();
        $acompte = $montant_total / 4;
        $solde = $montant_total - $acompte;
        $nb_mensualites = $request->nb_mensualites ?? 3;
        $montant_mensualite = $solde / $nb_mensualites;

        $vente = Vente::create([
            'numero_vente' => $numero,
            'client_id' => $request->client_id,
            'type_vente' => $request->type_vente,
            'kit_id' => $request->kit_id ?? null,
            'article_id' => $request->article_id ?? null,
            'quantite_article' => $request->quantite ?? 1,
            'montant_total' => $montant_total,
            'montant_ht' => $montant_total,
            'acompte' => $acompte,
            'solde' => $solde,
            'nb_mensualites' => $nb_mensualites,
            'montant_mensualite' => $montant_mensualite,
            'statut' => 'en_cours',
            'date_vente' => $request->date_vente ?? now(),
        ]);

        // Stock sortie si vente article
        if ($request->type_vente == 'article') {
            $article = Article::find($request->article_id);
            $article->stock -= $request->quantite;
            $article->save();

            Stock::create([
                'article_id' => $request->article_id,
                'type_mouvement' => 'sortie',
                'quantite' => $request->quantite,
                'prix_unitaire' => $article->prix_achat,
                'vente_id' => $vente->id,
                'motif' => 'Vente #' . $numero,
                'date_mouvement' => now(),
                'stock_avant' => $article->stock + $request->quantite,
                'stock_apres' => $article->stock,
            ]);
        }

        // Générer les échéances
        $date_echeance = Carbon::parse($vente->date_vente);
        for ($i = 1; $i <= $nb_mensualites; $i++) {
            $date_echeance->addMonth();
            Echeance::create([
                'vente_id' => $vente->id,
                'client_id' => $request->client_id,
                'date_echeance' => $date_echeance->copy(),
                'montant_dû' => $montant_mensualite,
                'statut' => 'en_attente'
            ]);
        }

        return redirect()->route('ventes.index')->with('success', 'Vente #' . $numero . ' enregistrée !');
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'kit', 'article', 'echeances']);
        return view('ventes.show', compact('vente'));
    }

    public function edit(Vente $vente)
    {
        $clients = Client::all();
        $kits = Kit::all();
        $articles = Article::where('stock', '>', 0)->get();
        return view('ventes.edit', compact('vente', 'clients', 'kits', 'articles'));
    }

    public function update(Request $request, Vente $vente)
    {
        $vente->update($request->all());
        return redirect()->route('ventes.index')->with('success', 'Vente modifiée !');
    }

    public function destroy(Vente $vente)
    {
        $vente->echeances()->delete();
        $vente->delete();
        return redirect()->route('ventes.index')->with('success', 'Vente supprimée !');
    }

    public function facture(Vente $vente)
    {
        $vente->load(['client', 'kit.articles', 'article', 'echeances']);
        $pdf = Pdf::loadView('pdf.facture', compact('vente'));
        return $pdf->download('facture-' . $vente->numero_vente . '.pdf');
    }
}