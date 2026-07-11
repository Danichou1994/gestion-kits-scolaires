<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Article;
use App\Models\Kit;
use App\Models\Vente;
use App\Models\Echeance;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Http\Request;

class SyncGoogleSheetsController extends Controller
{
    private function ensureJsonFile()
{
    $jsonPath = storage_path('app/google/service-account.json');
    
    if (!is_dir(dirname($jsonPath))) {
        mkdir(dirname($jsonPath), 0777, true);
    }
    
    // Si le fichier existe déjà, on l'utilise
    if (file_exists($jsonPath)) {
        return $jsonPath;
    }
    
    // Sinon, on essaie de créer à partir de la variable d'environnement
    $jsonContent = env('GOOGLE_SERVICE_ACCOUNT_JSON');
    if ($jsonContent) {
        file_put_contents($jsonPath, $jsonContent);
        return $jsonPath;
    }
    
    // Dernier recours : utiliser un service en ligne ou une autre méthode
    throw new \Exception('Fichier JSON introuvable.');
}

    public function index()
    {
        return view('sync-google-sheets.index');
    }

    public function syncAll()
    {
        try {
            $this->ensureJsonFile();
            $spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');

            // Clients
            $clients = Client::all();
            if ($clients->count() > 0) {
                $rows = $clients->map(function($client) {
                    return [
                        $client->id,
                        $client->nom,
                        $client->prenom,
                        $client->telephone,
                        $client->email ?? '-',
                        $client->adresse ?? '-',
                        $client->quartier ?? '-',
                        $client->created_at->format('d/m/Y')
                    ];
                })->toArray();

                Sheets::spreadsheet($spreadsheetId)
                    ->sheet('Clients')
                    ->clear()
                    ->append($rows);
            }

            // Articles
            $articles = Article::all();
            if ($articles->count() > 0) {
                $rows = $articles->map(function($article) {
                    return [
                        $article->id,
                        $article->nom_article,
                        $article->prix_achat,
                        $article->prix_vente,
                        $article->benefice,
                        $article->categorie,
                        $article->stock,
                        $article->seuil_alerte,
                        $article->fournisseur ?? '-'
                    ];
                })->toArray();

                Sheets::spreadsheet($spreadsheetId)
                    ->sheet('Articles')
                    ->clear()
                    ->append($rows);
            }

            // Kits
            $kits = Kit::with('articles')->get();
            if ($kits->count() > 0) {
                $rows = $kits->map(function($kit) {
                    $articlesList = $kit->articles->map(function($article) {
                        return $article->nom_article . ' (x' . $article->pivot->quantite . ')';
                    })->implode(', ');
                    
                    return [
                        $kit->id,
                        $kit->nom_kit,
                        $kit->prix_total,
                        $kit->reduction,
                        $kit->frais_livraison,
                        $kit->frais_carnet,
                        $kit->prix_final,
                        $articlesList
                    ];
                })->toArray();

                Sheets::spreadsheet($spreadsheetId)
                    ->sheet('Kits')
                    ->clear()
                    ->append($rows);
            }

            // Ventes
            $ventes = Vente::with(['client', 'kit'])->get();
            if ($ventes->count() > 0) {
                $rows = $ventes->map(function($vente) {
                    $statut = $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé');
                    return [
                        $vente->id,
                        $vente->numero_vente,
                        $vente->client->nom . ' ' . $vente->client->prenom,
                        $vente->kit->nom_kit ?? 'N/A',
                        $vente->montant_total,
                        $vente->acompte,
                        $vente->solde,
                        $vente->nb_mensualites,
                        $statut,
                        $vente->date_vente->format('d/m/Y')
                    ];
                })->toArray();

                Sheets::spreadsheet($spreadsheetId)
                    ->sheet('Ventes')
                    ->clear()
                    ->append($rows);
            }

            // Échéances
            $echeances = Echeance::with(['client', 'vente'])->get();
            if ($echeances->count() > 0) {
                $rows = $echeances->map(function($echeance) {
                    $statut = $echeance->statut == 'paye' ? 'Payé' : ($echeance->statut == 'en_attente' ? 'En attente' : 'En retard');
                    return [
                        $echeance->id,
                        $echeance->client->nom . ' ' . $echeance->client->prenom,
                        $echeance->vente->numero_vente ?? 'N/A',
                        $echeance->montant_dû,
                        $echeance->date_echeance->format('d/m/Y'),
                        $statut,
                        $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-'
                    ];
                })->toArray();

                Sheets::spreadsheet($spreadsheetId)
                    ->sheet('Echeances')
                    ->clear()
                    ->append($rows);
            }

            return redirect()->back()->with('success', '✅ Toutes les données synchronisées avec Google Sheets !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }
}