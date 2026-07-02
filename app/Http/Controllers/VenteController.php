<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Kit;
use App\Models\Echeance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with(['client', 'kit'])->get();
        return view('ventes.index', compact('ventes'));
    }

    public function create()
    {
        $clients = Client::all();
        $kits = Kit::all();
        return view('ventes.create', compact('clients', 'kits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'kit_id' => 'required|exists:kits,id',
            'montant_total' => 'required|numeric|min:0',
            'nb_mensualites' => 'required|integer|min:1|max:12',
            'date_vente' => 'required|date'
        ]);

        $montant_total = $request->montant_total;
        $acompte = $montant_total / 4;
        $solde = $montant_total - $acompte;
        $nb_mensualites = $request->nb_mensualites;
        $montant_mensualite = $solde / $nb_mensualites;

        // Créer la vente
        $vente = Vente::create([
            'client_id' => $request->client_id,
            'kit_id' => $request->kit_id,
            'montant_total' => $montant_total,
            'acompte' => $acompte,
            'solde' => $solde,
            'nb_mensualites' => $nb_mensualites,
            'montant_mensualite' => $montant_mensualite,
            'statut' => 'en_cours',
            'date_vente' => $request->date_vente
        ]);

        // Générer les échéances
        $date_echeance = Carbon::parse($request->date_vente);
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

        return redirect()->route('ventes.index')->with('success', 'Vente enregistrée avec succès ! Les échéances ont été générées.');
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'kit', 'echeances']);
        return view('ventes.show', compact('vente'));
    }

    public function edit(Vente $vente)
    {
        $clients = Client::all();
        $kits = Kit::all();
        return view('ventes.edit', compact('vente', 'clients', 'kits'));
    }

    public function update(Request $request, Vente $vente)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'kit_id' => 'required|exists:kits,id',
            'montant_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en_cours,termine,annule'
        ]);

        $vente->update($request->all());
        return redirect()->route('ventes.index')->with('success', 'Vente modifiée avec succès !');
    }

    public function destroy(Vente $vente)
    {
        // Supprimer les échéances associées
        $vente->echeances()->delete();
        $vente->delete();
        return redirect()->route('ventes.index')->with('success', 'Vente supprimée !');
    }
}