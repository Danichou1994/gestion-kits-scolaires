<?php

namespace App\Exports;

use App\Models\Echeance;
use Spatie\SimpleExcel\SimpleExcelWriter;

class EcheancesExportExcel
{
    public static function download()
    {
        try {
            $path = storage_path('app/temp/echeances.xlsx');
            
            if (!is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            
            $writer = SimpleExcelWriter::create($path);
            
            $writer->addHeader(['#', 'Client', 'Montant dû', "Date d'échéance", 'Statut', 'Date de paiement']);
            
            $echeances = Echeance::with(['client', 'vente'])->get();
            foreach ($echeances as $index => $echeance) {
                $writer->addRow([
                    $index + 1,
                    $echeance->client->prenom . ' ' . $echeance->client->nom,
                    $echeance->montant_dû,
                    $echeance->date_echeance->format('d/m/Y'),
                    $echeance->statut == 'en_attente' ? 'En attente' : ($echeance->statut == 'paye' ? 'Payé' : 'En retard'),
                    $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-'
                ]);
            }
            
            $writer->close();
            
            return response()->download($path, 'echeances-' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}