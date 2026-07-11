<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Article;
use App\Models\Kit;
use App\Models\Vente;
use App\Models\Echeance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class BackupController extends Controller
{
    // MÉTHODE STATIQUE - appelée depuis les modèles
    public static function autoBackup()
    {
        try {
            $date = date('Y-m-d');
            $folder = storage_path('app/backups/' . $date);
            
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            // === CLIENTS ===
            $clients = Client::all();
            $file = fopen($folder . '/clients.csv', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Adresse', 'Quartier']);
            foreach ($clients as $c) {
                fputcsv($file, [$c->id, $c->nom, $c->prenom, $c->telephone, $c->email ?? '-', $c->adresse ?? '-', $c->quartier ?? '-']);
            }
            fclose($file);

            // === ARTICLES ===
            $articles = Article::all();
            $file = fopen($folder . '/articles.csv', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Nom', 'Prix achat', 'Prix vente', 'Bénéfice', 'Catégorie', 'Stock', 'Seuil']);
            foreach ($articles as $a) {
                fputcsv($file, [$a->id, $a->nom_article, $a->prix_achat, $a->prix_vente, $a->benefice, $a->categorie, $a->stock, $a->seuil_alerte]);
            }
            fclose($file);

            // === KITS ===
            $kits = Kit::all();
            $file = fopen($folder . '/kits.csv', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Nom', 'Prix total', 'Réduction', 'Prix final']);
            foreach ($kits as $k) {
                fputcsv($file, [$k->id, $k->nom_kit, $k->prix_total, $k->reduction, $k->prix_final]);
            }
            fclose($file);

            // === VENTES ===
            $ventes = Vente::with(['client', 'kit'])->get();
            $file = fopen($folder . '/ventes.csv', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'N° Vente', 'Client', 'Kit', 'Total', 'Acompte', 'Solde', 'Statut', 'Date']);
            foreach ($ventes as $v) {
                fputcsv($file, [$v->id, $v->numero_vente, $v->client->nom . ' ' . $v->client->prenom, $v->kit->nom_kit ?? 'N/A', $v->montant_total, $v->acompte, $v->solde, $v->statut, $v->date_vente]);
            }
            fclose($file);

            // === ÉCHÉANCES ===
            $echeances = Echeance::with(['client'])->get();
            $file = fopen($folder . '/echeances.csv', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Client', 'Montant', 'Date échéance', 'Statut']);
            foreach ($echeances as $e) {
                fputcsv($file, [$e->id, $e->client->nom . ' ' . $e->client->prenom, $e->montant_dû, $e->date_echeance, $e->statut]);
            }
            fclose($file);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function downloadPDF()
    {
        $date = date('Y-m-d');
        $folder = storage_path('app/backups/' . $date);
        
        $clients = Client::all();
        $articles = Article::all();
        $ventes = Vente::with(['client', 'kit'])->get();
        $echeances = Echeance::with(['client'])->get();
        $kits = Kit::all();

        $pdf = Pdf::loadView('pdf.backup', compact('clients', 'articles', 'ventes', 'echeances', 'kits'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('rapport-' . $date . '.pdf');
    }

    public function downloadAll()
    {
        $date = date('Y-m-d');
        $folder = storage_path('app/backups/' . $date);
        
        // S'assurer que la sauvegarde existe
        self::autoBackup();
        
        $zipPath = storage_path('app/backups/backup-complet-' . $date . '.zip');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $files = glob($folder . '/*.csv');
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function backupNow()
    {
        $result = self::autoBackup();
        if ($result) {
            return redirect()->back()->with('success', '✅ Sauvegarde effectuée avec succès !');
        }
        return redirect()->back()->with('error', '❌ Erreur lors de la sauvegarde.');
    }
}