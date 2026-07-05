<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use App\Models\Echeance;
use Illuminate\Http\Request;

class GoogleSheetsController extends Controller
{
    public function index()
    {
        return view('google-sheets.index');
    }

    public function exportAll()
    {
        // Créer un fichier CSV
        $filename = storage_path('app/temp/export-complet.csv');
        
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
        
        $file = fopen($filename, 'w');
        
        // CLIENTS
        fputcsv($file, ['=== CLIENTS ===']);
        fputcsv($file, ['ID', 'Nom', 'Prénom', 'Téléphone', 'Adresse', 'Quartier', 'Date']);
        $clients = Client::all();
        foreach ($clients as $client) {
            fputcsv($file, [
                $client->id,
                $client->nom,
                $client->prenom,
                $client->telephone,
                $client->adresse ?? '-',
                $client->quartier ?? '-',
                $client->created_at->format('d/m/Y')
            ]);
        }
        fputcsv($file, []);
        
        // VENTES
        fputcsv($file, ['=== VENTES ===']);
        fputcsv($file, ['ID', 'Client', 'Kit', 'Total', 'Acompte', 'Solde', 'Statut', 'Date']);
        $ventes = Vente::with(['client', 'kit'])->get();
        foreach ($ventes as $vente) {
            fputcsv($file, [
                $vente->id,
                $vente->client->nom . ' ' . $vente->client->prenom,
                $vente->kit->nom_kit ?? 'N/A',
                $vente->montant_total,
                $vente->acompte,
                $vente->solde,
                $vente->statut == 'en_cours' ? 'En cours' : 'Terminé',
                $vente->date_vente->format('d/m/Y')
            ]);
        }
        fputcsv($file, []);
        
        // ÉCHÉANCES
        fputcsv($file, ['=== ÉCHÉANCES ===']);
        fputcsv($file, ['ID', 'Client', 'Montant', 'Date échéance', 'Statut']);
        $echeances = Echeance::with(['client'])->get();
        foreach ($echeances as $echeance) {
            fputcsv($file, [
                $echeance->id,
                $echeance->client->nom . ' ' . $echeance->client->prenom,
                $echeance->montant_dû,
                $echeance->date_echeance->format('d/m/Y'),
                $echeance->statut == 'paye' ? 'Payé' : 'En attente'
            ]);
        }
        
        fclose($file);
        
        return response()->download($filename, 'export-complet-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}