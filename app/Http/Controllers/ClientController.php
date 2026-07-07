<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|unique:clients,telephone',
            'email' => 'nullable|email|unique:clients,email',
        ]);

        Client::create($request->all());
        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès !');
    }

    public function show(Client $client)
    {
        $client->load(['ventes', 'echeances']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|unique:clients,telephone,' . $client->id,
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
        ]);

        $client->update($request->all());
        return redirect()->route('clients.index')->with('success', 'Client modifié !');
    }

    public function destroy(Client $client)
    {
        if (!$client->peutEtreSupprime()) {
            return redirect()->route('clients.index')->with('error', 'Impossible de supprimer ce client car il a des échéances en cours.');
        }

        if ($client->ventes()->count() > 0) {
            return redirect()->route('clients.index')->with('error', 'Impossible de supprimer ce client car il a des ventes.');
        }

        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé !');
    }

    public function exportCSV()
    {
        $clients = Client::all();
        $filename = storage_path('app/temp/clients.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['ID', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Adresse', 'Quartier']);

        foreach ($clients as $client) {
            fputcsv($file, [
                $client->id,
                $client->nom,
                $client->prenom,
                $client->telephone,
                $client->email ?? '-',
                $client->adresse ?? '-',
                $client->quartier ?? '-'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'clients-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }
}