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
    // Sauvegarde automatique après chaque modification
    public function autoBackup()
    {
        $date = date('Y-m-d');
        $folder = storage_path('app/backups/' . $date);
        
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $files = [];

        // === CLIENTS ===
        $clients = Client::all();
        $file = fopen($folder . '/clients.csv', 'w');
        fprintf($file, "\xEF\xBB\xBF"); // BOM pour UTF-8
        fputcsv($file, ['ID', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Adresse', 'Quartier', 'Date création']);
        foreach ($clients as $c) {
            fputcsv($file, [
                $c->id, 
                $c->nom, 
                $c->prenom, 
                $c->telephone, 
                $c->email ?? '-', 
                $c->adresse ?? '-', 
                $c->quartier ?? '-',
                $c->created_at->format('d/m/Y H:i')
            ]);
        }
        fclose($file);
        $files[] = $folder . '/clients.csv';

        // === ARTICLES ===
        $articles = Article::all();
        $file = fopen($folder . '/articles.csv', 'w');
        fprintf($file, "\xEF\xBB\xBF");
        fputcsv($file, ['ID', 'Nom', 'Prix achat', 'Prix vente', 'Bénéfice', 'Catégorie', 'Stock', 'Seuil', 'Fournisseur']);
        foreach ($articles as $a) {
            fputcsv($file, [
                $a->id, 
                $a->nom_article, 
                $a->prix_achat, 
                $a->prix_vente, 
                $a->benefice, 
                $a->categorie, 
                $a->stock, 
                $a->seuil_alerte,
                $a->fournisseur ?? '-'
            ]);
        }
        fclose($file);
        $files[] = $folder . '/articles.csv';

        // === KITS ===
        $kits = Kit::all();
        $file = fopen($folder . '/kits.csv', 'w');
        fprintf($file, "\xEF\xBB\xBF");
        fputcsv($file, ['ID', 'Nom', 'Prix total', 'Réduction', 'Livraison', 'Carnet', 'Prix final']);
        foreach ($kits as $k) {
            fputcsv($file, [
                $k->id, 
                $k->nom_kit, 
                $k->prix_total, 
                $k->reduction, 
                $k->frais_livraison, 
                $k->frais_carnet, 
                $k->prix_final
            ]);
        }
        fclose($file);
        $files[] = $folder . '/kits.csv';

        // === VENTES ===
        $ventes = Vente::with(['client', 'kit'])->get();
        $file = fopen($folder . '/ventes.csv', 'w');
        fprintf($file, "\xEF\xBB\xBF");
        fputcsv($file, ['ID', 'N° Vente', 'Client', 'Kit', 'Total', 'Acompte', 'Solde', 'Mensualités', 'Statut', 'Date']);
        foreach ($ventes as $v) {
            fputcsv($file, [
                $v->id, 
                $v->numero_vente, 
                $v->client->nom . ' ' . $v->client->prenom, 
                $v->kit->nom_kit ?? 'N/A', 
                $v->montant_total, 
                $v->acompte, 
                $v->solde, 
                $v->nb_mensualites,
                $v->statut, 
                $v->date_vente->format('d/m/Y')
            ]);
        }
        fclose($file);
        $files[] = $folder . '/ventes.csv';

        // === ÉCHÉANCES ===
        $echeances = Echeance::with(['client'])->get();
        $file = fopen($folder . '/echeances.csv', 'w');
        fprintf($file, "\xEF\xBB\xBF");
        fputcsv($file, ['ID', 'Client', 'Montant', 'Date échéance', 'Statut', 'Date paiement']);
        foreach ($echeances as $e) {
            fputcsv($file, [
                $e->id, 
                $e->client->nom . ' ' . $e->client->prenom, 
                $e->montant_dû, 
                $e->date_echeance->format('d/m/Y'), 
                $e->statut,
                $e->date_paiement ? $e->date_paiement->format('d/m/Y') : '-'
            ]);
        }
        fclose($file);
        $files[] = $folder . '/echeances.csv';

        // === SAUVEGARDE PDF ===
        $this->generatePDFBackup($folder, $date);

        return response()->json(['success' => true, 'files' => count($files)]);
    }

    // Générer un PDF avec toutes les données
    public function generatePDFBackup($folder, $date)
    {
        $clients = Client::all();
        $articles = Article::all();
        $ventes = Vente::with(['client', 'kit'])->get();
        $echeances = Echeance::with(['client'])->get();
        $kits = Kit::all();

        $pdf = Pdf::loadView('pdf.backup', compact('clients', 'articles', 'ventes', 'echeances', 'kits'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->save($folder . '/rapport-' . $date . '.pdf');
    }

    // Télécharger le PDF
    public function downloadPDF()
    {
        $date = date('Y-m-d');
        $folder = storage_path('app/backups/' . $date);
        
        // Générer le PDF s'il n'existe pas
        $this->generatePDFBackup($folder, $date);
        
        $path = $folder . '/rapport-' . $date . '.pdf';
        
        if (file_exists($path)) {
            return response()->download($path)->deleteFileAfterSend(true);
        }
        
        return redirect()->back()->with('error', 'PDF non trouvé.');
    }

    // Télécharger toutes les sauvegardes en ZIP
    public function downloadAll()
    {
        $date = date('Y-m-d');
        $folder = storage_path('app/backups/' . $date);
        
        $zipPath = storage_path('app/backups/backup-complet-' . $date . '.zip');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $files = glob($folder . '/*');
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}