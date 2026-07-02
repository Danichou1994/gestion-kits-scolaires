<?php

namespace App\Exports;

use App\Models\Client;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ClientsExportExcel
{
    public static function download()
    {
        try {
            $path = storage_path('app/temp/clients.xlsx');
            
            if (!is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0777, true);
            }
            
            $writer = SimpleExcelWriter::create($path);
            
            $writer->addHeader(['#', 'Nom', 'Prénom', 'Téléphone', 'Adresse', 'Quartier', "Date d'inscription"]);
            
            $clients = Client::all();
            foreach ($clients as $index => $client) {
                $writer->addRow([
                    $index + 1,
                    $client->nom,
                    $client->prenom,
                    $client->telephone,
                    $client->adresse ?? 'Non renseignée',
                    $client->quartier ?? 'Non renseigné',
                    $client->created_at->format('d/m/Y')
                ]);
            }
            
            $writer->close();
            
            return response()->download($path, 'clients-' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}