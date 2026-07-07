<?php

namespace App\Http\Controllers;

use App\Models\Echeance;
use App\Models\Vente;
use Illuminate\Http\Request;

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
        
        $ventesAvecEcheances = Vente::with(['client', 'echeances'])
                                   ->whereHas('echeances')
                                   ->get();
        
        return view('echeances.index', compact(
            'echeances', 'echeancesAJour', 'echeancesRetard',
            'echeancesPayees', 'ventesAvecEcheances'
        ));
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'echeances']);
        return view('echeances.show', compact('vente'));
    }

    public function marquerPayee(Echeance $echeance)
    {
        $echeance->update([
            'statut' => 'paye',
            'date_paiement' => today()
        ]);

        $vente = $echeance->vente;
        $echeancesRestantes = $vente->echeances()->where('statut', '!=', 'paye')->count();
        
        if ($echeancesRestantes == 0) {
            $vente->update(['statut' => 'termine']);
        }

        return redirect()->route('echeances.index')->with('success', 'Paiement enregistré !');
    }

    public function marquerRetard(Echeance $echeance)
    {
        $echeance->update(['statut' => 'en_retard']);
        return redirect()->route('echeances.index')->with('success', 'Échéance marquée en retard.');
    }
}