<?php

namespace App\Exports;

use App\Models\Vente;
use Spatie\SimpleExcel\SimpleExcelWriter;

class VentesExportExcel
{
    public static function download()
    {
        try {
            $path = storage_path('app/temp/ventes.xlsx');
            
            if (!is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            
            $writer = SimpleExcelWriter::create($path);
            
            $writer->addHeader([
                '#', 'Client', 'Kit', 'Montant total', 'Acompte', 
                'Solde', 'Nb mensualités', 'Montant/mensualité', 'Statut', 'Date de vente'
            ]);
            
            $ventes = Vente::with(['client', 'kit'])->get();
            foreach ($ventes as $index => $vente) {
                $writer->addRow([
                    $index + 1,
                    $vente->client->prenom . ' ' . $vente->client->nom,
                    $vente->kit->nom_kit ?? 'N/A',
                    $vente->montant_total,
                    $vente->acompte,
                    $vente->solde,
                    $vente->nb_mensualites,
                    $vente->montant_mensualite,
                    $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé'),
                    $vente->date_vente->format('d/m/Y')
                ]);
            }
            
            $writer->close();
            
            return response()->download($path, 'ventes-' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}