<?php

namespace App\Http\Controllers;

use App\Models\Echeance;
use App\Models\Vente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EcheanceController extends Controller
{
    public function index()
    {
        $echeances = Echeance::with(['client', 'vente'])
                            ->orderBy('date_echeance', 'asc')
                            ->get();
        
        $echeancesAJour = $echeances->where('statut', 'en_attente')
                                    ->where('date_echeance', '>=', today())
                                    ->count();
        
        $echeancesRetard = $echeances->where('statut', 'en_attente')
                                    ->where('date_echeance', '<', today())
                                    ->count();
        
        $echeancesPayees = $echeances->where('statut', 'paye')->count();
        
        return view('echeances.index', compact('echeances', 'echeancesAJour', 'echeancesRetard', 'echeancesPayees'));
    }

    public function create()
    {
        $ventes = Vente::with('client')->where('statut', 'en_cours')->get();
        return view('echeances.create', compact('ventes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vente_id' => 'required|exists:ventes,id',
            'date_echeance' => 'required|date',
            'montant_dû' => 'required|numeric|min:0'
        ]);

        $vente = Vente::find($request->vente_id);
        
        Echeance::create([
            'vente_id' => $request->vente_id,
            'client_id' => $vente->client_id,
            'date_echeance' => $request->date_echeance,
            'montant_dû' => $request->montant_dû,
            'statut' => 'en_attente'
        ]);

        return redirect()->route('echeances.index')->with('success', 'Échéance ajoutée avec succès !');
    }

    public function show(Echeance $echeance)
    {
        $echeance->load(['client', 'vente']);
        return view('echeances.show', compact('echeance'));
    }

    public function edit(Echeance $echeance)
    {
        $ventes = Vente::with('client')->where('statut', 'en_cours')->get();
        return view('echeances.edit', compact('echeance', 'ventes'));
    }

    public function update(Request $request, Echeance $echeance)
    {
        $request->validate([
            'date_echeance' => 'required|date',
            'montant_dû' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,paye,en_retard'
        ]);

        $echeance->update($request->all());
        return redirect()->route('echeances.index')->with('success', 'Échéance modifiée avec succès !');
    }

    public function destroy(Echeance $echeance)
    {
        $echeance->delete();
        return redirect()->route('echeances.index')->with('success', 'Échéance supprimée !');
    }

    // Action pour marquer une échéance comme payée
    public function marquerPayee(Echeance $echeance)
    {
        $echeance->update([
            'statut' => 'paye',
            'date_paiement' => today()
        ]);

        // Vérifier si toutes les échéances de la vente sont payées
        $vente = $echeance->vente;
        $echeancesRestantes = $vente->echeances()->where('statut', '!=', 'paye')->count();
        
        if ($echeancesRestantes == 0) {
            $vente->update(['statut' => 'termine']);
        }

        return redirect()->route('echeances.index')->with('success', 'Échéance marquée comme payée !');
    }

    // Action pour marquer une échéance comme en retard
    public function marquerRetard(Echeance $echeance)
    {
        $echeance->update(['statut' => 'en_retard']);
        return redirect()->route('echeances.index')->with('success', 'Échéance marquée comme en retard !');
    }
}