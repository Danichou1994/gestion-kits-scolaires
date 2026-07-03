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
        
        // En-tête
        $writer->addRow(['=== RAPPORT COMPLET ===']);
        $writer->addRow(['Généré le : ' . now()->format('d/m/Y H:i')]);
        $writer->addRow([]);

        // ====== CLIENTS ======
        $writer->addRow(['=== CLIENTS ===']);
        $writer->addRow(['#', 'Nom', 'Prénom', 'Téléphone', 'Adresse', 'Quartier', "Date d'inscription"]);
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
        $writer->addRow([]);

        // ====== KITS ======
        $writer->addRow(['=== KITS ===']);
        $writer->addRow(['#', 'Nom du kit', 'Description', 'Prix total']);
        $kits = Kit::all();
        foreach ($kits as $index => $kit) {
            $writer->addRow([
                $index + 1,
                $kit->nom_kit,
                $kit->description ?? '-',
                $kit->prix_total
            ]);
        }
        $writer->addRow([]);

        // ====== ARTICLES ======
        $writer->addRow(['=== ARTICLES ===']);
        $writer->addRow(['#', 'Nom', 'Prix unitaire', 'Catégorie', 'Stock', 'Seuil alerte']);
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
        $writer->addRow([]);

        // ====== VENTES ======
        $writer->addRow(['=== VENTES ===']);
        $writer->addRow(['#', 'Client', 'Kit', 'Total', 'Acompte', 'Solde', 'Mensualités', 'Statut', 'Date']);
        $ventes = Vente::with(['client', 'kit'])->get();
        foreach ($ventes as $index => $vente) {
            $statut = $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé');
            $writer->addRow([
                $index + 1,
                $vente->client->prenom . ' ' . $vente->client->nom,
                $vente->kit->nom_kit ?? 'N/A',
                $vente->montant_total,
                $vente->acompte,
                $vente->solde,
                $vente->nb_mensualites,
                $statut,
                $vente->date_vente->format('d/m/Y')
            ]);
        }
        $writer->addRow([]);

        // ====== ÉCHÉANCES ======
        $writer->addRow(['=== ÉCHÉANCES ===']);
        $writer->addRow(['#', 'Client', 'Vente', 'Montant dû', "Date d'échéance", 'Statut', 'Date paiement']);
        $echeances = Echeance::with(['client', 'vente'])->get();
        foreach ($echeances as $index => $echeance) {
            if ($echeance->statut == 'paye') {
                $statutEcheance = 'Payé';
            } elseif ($echeance->statut == 'en_attente') {
                $statutEcheance = 'En attente';
            } else {
                $statutEcheance = 'En retard';
            }
            
            $writer->addRow([
                $index + 1,
                $echeance->client->prenom . ' ' . $echeance->client->nom,
                $echeance->vente->id ?? 'N/A',
                $echeance->montant_dû,
                $echeance->date_echeance->format('d/m/Y'),
                $statutEcheance,
                $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-'
            ]);
        }

        $writer->close();

        return response()->download($path, 'rapport-complet-' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
    }
}