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

    public function exportAll()
    {
        try {
            // ====== EXPORTER LES CLIENTS ======
            $clients = Client::all();
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

            if (!empty($rows)) {
                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Clients')
                    ->clear()
                    ->append($rows);
            }

            // ====== EXPORTER LES ARTICLES ======
            $articles = Article::all();
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

            if (!empty($rows)) {
                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Articles')
                    ->clear()
                    ->append($rows);
            }

            // ====== EXPORTER LES KITS ======
            $kits = Kit::with('articles')->get();
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

            if (!empty($rows)) {
                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Kits')
                    ->clear()
                    ->append($rows);
            }

            // ====== EXPORTER LES VENTES ======
            $ventes = Vente::with(['client', 'kit'])->get();
            $rows = $ventes->map(function($vente) {
                return [
                    $vente->id,
                    $vente->client->nom . ' ' . $vente->client->prenom,
                    $vente->kit->nom_kit ?? 'N/A',
                    $vente->montant_total,
                    $vente->acompte,
                    $vente->solde,
                    $vente->nb_mensualites,
                    $vente->montant_mensualite,
                    $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé'),
                    $vente->date_vente->format('d/m/Y')
                ];
            })->toArray();

            if (!empty($rows)) {
                Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
                    ->sheet('Ventes')
                    ->clear()
                    ->append($rows);
            }

            // ====== EXPORTER LES ÉCHÉANCES ======
            $echeances = Echeance::with(['client', 'vente'])->get();
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

            if (!empty($rows)) {
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

    // Exporter uniquement une table spécifique
    public function exportClients()
    {
        $clients = Client::all();
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

        return redirect()->back()->with('success', '✅ Clients exportés avec succès !');
    }

    public function exportVentes()
    {
        $ventes = Vente::with(['client', 'kit'])->get();
        $rows = $ventes->map(function($vente) {
            return [
                $vente->id,
                $vente->client->nom . ' ' . $vente->client->prenom,
                $vente->kit->nom_kit ?? 'N/A',
                $vente->montant_total,
                $vente->acompte,
                $vente->solde,
                $vente->nb_mensualites,
                $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé'),
                $vente->date_vente->format('d/m/Y')
            ];
        })->toArray();

        Sheets::spreadsheet(env('GOOGLE_SHEETS_SPREADSHEET_ID'))
            ->sheet('Ventes')
            ->clear()
            ->append($rows);

        return redirect()->back()->with('success', '✅ Ventes exportées avec succès !');
    }
}