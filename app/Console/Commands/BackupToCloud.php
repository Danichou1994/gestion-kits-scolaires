<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupToCloud extends Command
{
    protected $signature = 'backup:cloud';
    protected $description = 'Sauvegarde vers le cloud';

    public function handle()
    {
        $this->info('Début de la sauvegarde vers le cloud...');
        
        // Nom du fichier
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backup/' . $filename);
        
        // Créer le dossier
        if (!is_dir(storage_path('app/backup'))) {
            mkdir(storage_path('app/backup'), 0777, true);
        }
        
        // Exporter la base de données
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE'),
            $path
        );
        exec($command);
        
        // Upload vers le cloud si configuré
        if (env('BACKUP_DISK') && env('BACKUP_DISK') !== 'local') {
            Storage::disk(env('BACKUP_DISK'))->put('backups/' . $filename, file_get_contents($path));
            $this->info('Fichier envoyé vers le cloud !');
        }
        
        // Supprimer les sauvegardes locales de plus de 7 jours
        $files = Storage::disk('local')->files('app/backup');
        foreach ($files as $file) {
            $timestamp = Storage::disk('local')->lastModified($file);
            if (Carbon::createFromTimestamp($timestamp)->diffInDays(now()) > 7) {
                Storage::disk('local')->delete($file);
            }
        }
        
        $this->info('Sauvegarde terminée !');
    }
}