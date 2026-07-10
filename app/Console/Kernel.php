<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Sauvegarde vers Google Sheets tous les jours à 23h
        $schedule->command('backup:database')->dailyAt('23:00');
        
        // Sauvegarde vers le cloud tous les jours à 02h
        $schedule->command('backup:cloud')->dailyAt('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}