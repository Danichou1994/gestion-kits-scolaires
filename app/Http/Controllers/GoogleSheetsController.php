<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Article;
use App\Models\Kit;
use App\Models\Vente;
use App\Models\Echeance;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GoogleSheetsController extends Controller
{
    public function index()
    {
        return view('google-sheets.index');
    }

    public function exportAll()
    {
        try {
            // === CRÉER LE FICHIER JSON À PARTIR DE LA CONFIGURATION ===
            $config = config('google-sheets.service_account');
            $jsonPath = storage_path('app/google/service-account.json');
            
            if (!is_dir(dirname($jsonPath))) {
                mkdir(dirname($jsonPath), 0777, true);
            }
            
            file_put_contents($jsonPath, json_encode($config, JSON_PRETTY_PRINT));
            
            if (!file_exists($jsonPath)) {
                return redirect()->back()->with('error', '❌ Impossible de créer le fichier JSON.');
            }

            // === EXPORTER LES CLIENTS ===
            $clients = Client::all();
            if ($clients->count() > 0) {
                $rows = $clients->map(function($client) {
                    return [
                        $client->id,
                        $client->nom,
                        $client->prenom,
                        $client->telephone,
                        $client->adresse ?? '-',
                        $client->quartier ?? '-',
                        $client->created_at->format('d/m/Y')
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Clients')
                    ->clear()
                    ->append($rows);
            }

            // === EXPORTER LES ARTICLES ===
            $articles = Article::all();
            if ($articles->count() > 0) {
                $rows = $articles->map(function($article) {
                    return [
                        $article->id,
                        $article->nom_article,
                        $article->prix_unitaire,
                        $article->categorie,
                        $article->stock,
                        $article->seuil_alerte
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Articles')
                    ->clear()
                    ->append($rows);
            }

            // === EXPORTER LES KITS ===
            $kits = Kit::with('articles')->get();
            if ($kits->count() > 0) {
                $rows = $kits->map(function($kit) {
                    $articlesList = $kit->articles->map(function($article) {
                        return $article->nom_article . ' (x' . $article->pivot->quantite . ')';
                    })->implode(', ');
                    
                    return [
                        $kit->id,
                        $kit->nom_kit,
                        $kit->description ?? '-',
                        $kit->prix_total,
                        $articlesList
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Kits')
                    ->clear()
                    ->append($rows);
            }

            // === EXPORTER LES VENTES ===
            $ventes = Vente::with(['client', 'kit'])->get();
            if ($ventes->count() > 0) {
                $rows = $ventes->map(function($vente) {
                    $statut = $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé');
                    return [
                        $vente->id,
                        $vente->client->nom . ' ' . $vente->client->prenom,
                        $vente->kit->nom_kit ?? 'N/A',
                        $vente->montant_total,
                        $vente->acompte,
                        $vente->solde,
                        $vente->nb_mensualites,
                        $vente->montant_mensualite,
                        $statut,
                        $vente->date_vente->format('d/m/Y')
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Ventes')
                    ->clear()
                    ->append($rows);
            }

            // === EXPORTER LES ÉCHÉANCES ===
            $echeances = Echeance::with(['client', 'vente'])->get();
            if ($echeances->count() > 0) {
                $rows = $echeances->map(function($echeance) {
                    $statut = $echeance->statut == 'paye' ? 'Payé' : ($echeance->statut == 'en_attente' ? 'En attente' : 'En retard');
                    return [
                        $echeance->id,
                        $echeance->client->nom . ' ' . $echeance->client->prenom,
                        $echeance->vente->id ?? 'N/A',
                        $echeance->montant_dû,
                        $echeance->date_echeance->format('d/m/Y'),
                        $statut,
                        $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-'
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Echeances')
                    ->clear()
                    ->append($rows);
            }

            return redirect()->back()->with('success', '✅ Toutes les données ont été synchronisées avec Google Sheets !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }
}