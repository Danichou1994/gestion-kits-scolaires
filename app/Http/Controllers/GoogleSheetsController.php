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
            // === CRÉER LE FICHIER JSON SI NÉCESSAIRE ===
            $jsonContent = env('GOOGLE_SERVICE_ACCOUNT_JSON');
            $path = storage_path('app/google/service-account.json');
            
            if ($jsonContent) {
                // Créer le dossier
                if (!is_dir(dirname($path))) {
                    mkdir(dirname($path), 0777, true);
                }
                // Écrire le fichier
                file_put_contents($path, $jsonContent);
            }
            
            // Vérifier que le fichier existe
            if (!file_exists($path)) {
                return redirect()->back()->with('error', '❌ Fichier JSON introuvable. Vérifie que GOOGLE_SERVICE_ACCOUNT_JSON est définie sur Render.');
            }

            // === CLIENTS ===
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

                Sheets::spreadsheet(config('google-sheets.spreadsheet_id'))
                    ->sheet('Clients')
                    ->clear()
                    ->append($rows);
            }

            // === ARTICLES ===
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

                Sheets::spreadsheet(config('google-sheets.spreadsheet_id'))
                    ->sheet('Articles')
                    ->clear()
                    ->append($rows);
            }

            // === KITS ===
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

                Sheets::spreadsheet(config('google-sheets.spreadsheet_id'))
                    ->sheet('Kits')
                    ->clear()
                    ->append($rows);
            }

            // === VENTES ===
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

                Sheets::spreadsheet(config('google-sheets.spreadsheet_id'))
                    ->sheet('Ventes')
                    ->clear()
                    ->append($rows);
            }

            // === ÉCHÉANCES ===
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

                Sheets::spreadsheet(config('google-sheets.spreadsheet_id'))
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