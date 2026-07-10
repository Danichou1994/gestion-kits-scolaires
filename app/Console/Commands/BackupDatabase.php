<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BackupToGoogleSheets;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Sauvegarde la base de données vers Google Sheets';

    public function handle()
    {
        $this->info('Début de la sauvegarde...');
        
        BackupToGoogleSheets::dispatch();
        
        $this->info('Sauvegarde envoyée en arrière-plan vers Google Sheets !');
    }
}