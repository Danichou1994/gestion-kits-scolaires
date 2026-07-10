<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupToCloud extends Command
{
    protected $signature = 'backup:cloud';
    protected $description = 'Sauvegarde vers le cloud';

    public function handle()
    {
        $this->info('Début de la sauvegarde vers le cloud...');
        
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backup/' . $filename);
        
        if (!is_dir(storage_path('app/backup'))) {
            mkdir(storage_path('app/backup'), 0777, true);
        }
        
        // Utiliser les commandes artisan pour la sauvegarde
        $this->call('backup:run', ['--only-db' => true]);
        
        $this->info('Sauvegarde terminée !');
    }
}