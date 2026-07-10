<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Article;
use App\Models\Kit;
use App\Models\Vente;
use App\Models\Echeance;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BackupToGoogleSheets implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        
        // Exporter les clients
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

        // Exporter les articles
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

        // Exporter les kits
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

        // Exporter les ventes
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

        // Exporter les échéances
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
    }
}