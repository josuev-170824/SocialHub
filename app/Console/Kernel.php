<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // ejecuta cada minuto para validar las publicaciones programadas
        $schedule->command('publicaciones:ejecutar-programadas')
                ->everyMinute()
                ->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/publicaciones-programadas.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}