<?php

namespace App\Exports;

use App\Models\Client;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ClientsExcelExport
{
    public static function download()
    {
        $path = storage_path('app/temp/clients.xlsx');
        
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
    }
}