<?php

namespace App\Http\Controllers;

use App\Models\Echeance;
use App\Models\Vente;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EcheanceController extends Controller
{
    public function index()
    {
        // Toutes les échéances avec les relations
        $echeances = Echeance::with(['client', 'vente'])
                            ->orderBy('date_echeance', 'asc')
                            ->get();
        
        // Statistiques
        $totalEcheances = $echeances->count();
        $totalPayees = $echeances->where('statut', 'paye')->count();
        $totalAttente = $echeances->where('statut', 'en_attente')->count();
        $totalRetard = $echeances->where('statut', 'en_retard')->count();
        $montantTotalDu = $echeances->where('statut', '!=', 'paye')->sum('montant_dû');
        
        // Échéances du jour
        $echeancesAujourdhui = $echeances->filter(function($e) {
            return $e->date_echeance->isToday() && $e->statut != 'paye';
        });
        
        // Échéances en retard
        $echeancesRetard = $echeances->filter(function($e) {
            return $e->date_echeance->isPast() && $e->statut != 'paye';
        });
        
        // Ventes avec leurs échéances pour une vue groupée
        $ventesAvecEcheances = Vente::with(['client', 'echeances'])
                                   ->whereHas('echeances')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('echeances.index', compact(
            'echeances',
            'totalEcheances',
            'totalPayees',
            'totalAttente',
            'totalRetard',
            'montantTotalDu',
            'echeancesAujourdhui',
            'echeancesRetard',
            'ventesAvecEcheances'
        ));
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'echeances']);
        return view('echeances.show', compact('vente'));
    }

    public function showClient(Client $client)
    {
        $client->load(['ventes.echeances']);
        return view('echeances.client', compact('client'));
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

        return redirect()->route('echeances.index')->with('success', '✅ Paiement enregistré !');
    }

    public function marquerRetard(Echeance $echeance)
    {
        $echeance->update(['statut' => 'en_retard']);
        return redirect()->route('echeances.index')->with('success', '⚠️ Échéance marquée en retard.');
    }

    public function marquerAttente(Echeance $echeance)
    {
        $echeance->update(['statut' => 'en_attente']);
        return redirect()->route('echeances.index')->with('success', '🔄 Échéance remise en attente.');
    }

    public function exportCSV()
    {
        $echeances = Echeance::with(['client', 'vente'])->get();
        $filename = storage_path('app/temp/echeances.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['#', 'Client', 'Vente', 'Montant', "Date d'échéance", 'Statut', 'Date paiement']);

        foreach ($echeances as $index => $e) {
            fputcsv($file, [
                $index + 1,
                $e->client->nom . ' ' . $e->client->prenom,
                $e->vente->numero_vente ?? 'N/A',
                $e->montant_dû,
                $e->date_echeance->format('d/m/Y'),
                $e->statut == 'paye' ? 'Payé' : ($e->statut == 'en_attente' ? 'En attente' : 'En retard'),
                $e->date_paiement ? $e->date_paiement->format('d/m/Y') : '-'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'echeances-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}