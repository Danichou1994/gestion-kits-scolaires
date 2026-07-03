<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Article;
use App\Models\Kit;
use App\Models\Vente;
use App\Models\Echeance;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Http\Request;

class GoogleSheetsController extends Controller
{
    public function index()
    {
        return view('google-sheets.index');
    }

    private function ensureJsonFile()
    {
        $jsonContent = env('GOOGLE_SERVICE_ACCOUNT_JSON');
        
        if (!$jsonContent) {
            throw new \Exception('La variable GOOGLE_SERVICE_ACCOUNT_JSON n\'est pas définie.');
        }
        
        $path = storage_path('app/google/service-account.json');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $jsonContent);
        
        return $path;
    }

    public function exportAll()
    {
        try {
            $this->ensureJsonFile();
            
            // Clients
            $clients = Client::all();
            if ($clients->count() > 0) {
                $rows = $clients->map(function($client) {
                    return [
                        $client->id, $client->nom, $client->prenom,
                        $client->telephone, $client->adresse ?? '-',
                        $client->quartier ?? '-',
                        $client->created_at->format('d/m/Y')
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Clients')
                    ->clear()
                    ->append($rows);
            }

            // Articles
            $articles = Article::all();
            if ($articles->count() > 0) {
                $rows = $articles->map(function($article) {
                    return [
                        $article->id, $article->nom_article,
                        $article->prix_unitaire, $article->categorie,
                        $article->stock, $article->seuil_alerte
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
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
                        $kit->id, $kit->nom_kit,
                        $kit->description ?? '-',
                        $kit->prix_total, $articlesList
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
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
                        $vente->client->nom . ' ' . $vente->client->prenom,
                        $vente->kit->nom_kit ?? 'N/A',
                        $vente->montant_total, $vente->acompte,
                        $vente->solde, $vente->nb_mensualites,
                        $vente->montant_mensualite, $statut,
                        $vente->date_vente->format('d/m/Y')
                    ];
                })->toArray();

                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Ventes')
                    ->clear()
                    ->append($rows);
            }

            // Echeances
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

    public function exportClients()
    {
        try {
            $this->ensureJsonFile();
            
            $clients = Client::all();
            $rows = $clients->map(function($client) {
                return [
                    $client->id, $client->nom, $client->prenom,
                    $client->telephone, $client->adresse ?? '-',
                    $client->quartier ?? '-',
                    $client->created_at->format('d/m/Y')
                ];
            })->toArray();

            Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                ->sheet('Clients')
                ->clear()
                ->append($rows);

            return redirect()->back()->with('success', '✅ Clients exportés avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }

    public function exportVentes()
    {
        try {
            $this->ensureJsonFile();
            
            $ventes = Vente::with(['client', 'kit'])->get();
            $rows = $ventes->map(function($vente) {
                $statut = $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé');
                return [
                    $vente->id,
                    $vente->client->nom . ' ' . $vente->client->prenom,
                    $vente->kit->nom_kit ?? 'N/A',
                    $vente->montant_total, $vente->acompte,
                    $vente->solde, $vente->nb_mensualites,
                    $statut,
                    $vente->date_vente->format('d/m/Y')
                ];
            })->toArray();

            Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                ->sheet('Ventes')
                ->clear()
                ->append($rows);

            return redirect()->back()->with('success', '✅ Ventes exportées avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Erreur : ' . $e->getMessage());
        }
    }
}