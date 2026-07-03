<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use App\Models\Echeance;
use App\Models\Kit;
use App\Models\Article;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\SimpleExcel\SimpleExcelWriter;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index');
    }

    public function exportPDF()
    {
        $clients = Client::all();
        $ventes = Vente::with(['client', 'kit'])->get();
        $echeances = Echeance::with(['client', 'vente'])->get();
        $kits = Kit::with('articles')->get();
        $articles = Article::all();

        $pdf = Pdf::loadView('pdf.rapport-complet', compact(
            'clients', 'ventes', 'echeances', 'kits', 'articles'
        ));

        return $pdf->download('rapport-complet-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        $path = storage_path('app/temp/rapport-complet.xlsx');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $writer = SimpleExcelWriter::create($path);

        // ====== Feuille 1 : Clients ======
        $writer->addSheet('Clients');
        $writer->addHeader(['#', 'Nom', 'Prénom', 'Téléphone', 'Adresse', 'Quartier', 'Date inscription']);
        $clients = Client::all();
        foreach ($clients as $index => $client) {
            $writer->addRow([
                $index + 1,
                $client->nom,
                $client->prenom,
                $client->telephone,
                $client->adresse ?? '-',
                $client->quartier ?? '-',
                $client->created_at->format('d/m/Y')
            ]);
        }

        // ====== Feuille 2 : Kits ======
        $writer->addSheet('Kits');
        $writer->addHeader(['#', 'Nom du kit', 'Description', 'Prix total']);
        $kits = Kit::all();
        foreach ($kits as $index => $kit) {
            $writer->addRow([
                $index + 1,
                $kit->nom_kit,
                $kit->description ?? '-',
                $kit->prix_total
            ]);
        }

        // ====== Feuille 3 : Articles ======
        $writer->addSheet('Articles');
        $writer->addHeader(['#', 'Nom', 'Prix unitaire', 'Catégorie', 'Stock', 'Seuil alerte']);
        $articles = Article::all();
        foreach ($articles as $index => $article) {
            $writer->addRow([
                $index + 1,
                $article->nom_article,
                $article->prix_unitaire,
                $article->categorie,
                $article->stock,
                $article->seuil_alerte
            ]);
        }

        // ====== Feuille 4 : Ventes ======
        $writer->addSheet('Ventes');
        $writer->addHeader(['#', 'Client', 'Kit', 'Total', 'Acompte', 'Solde', 'Mensualités', 'Statut', 'Date']);
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
                $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé'),
                $vente->date_vente->format('d/m/Y')
            ]);
        }

        // ====== Feuille 5 : Échéances ======
        $writer->addSheet('Échéances');
        $writer->addHeader(['#', 'Client', 'Vente', 'Montant dû', 'Date échéance', 'Statut', 'Date paiement']);
        $echeances = Echeance::with(['client', 'vente'])->get();
        foreach ($echeances as $index => $echeance) {
            $writer->addRow([
                $index + 1,
                $echeance->client->prenom . ' ' . $echeance->client->nom,
                $echeance->vente->id ?? 'N/A',
                $echeance->montant_dû,
                $echeance->date_echeance->format('d/m/Y'),
                $echeance->statut == 'paye' ? 'Payé' : ($echeance->statut == 'en_attente' ? 'En attente' : 'En retard'),
                $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-'
            ]);
        }

        $writer->close();

        return response()->download($path, 'rapport-complet-' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
    }
}