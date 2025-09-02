<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckTaskDeadlines::class,
        Commands\SyncTasksToCloud::class,
        Commands\GenerateTestData::class,
        Commands\GenerateDashboardData::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Verificar prazos de tarefas diariamente às 9h da manhã
        $schedule->command('tasks:check-deadlines --notify')
                 ->dailyAt('09:00')
                 ->description('Verificar prazos de tarefas e enviar notificações');

        // Sincronizar tarefas com a nuvem a cada 6 horas
        $schedule->command('tasks:sync-cloud')
                 ->everyFourHours()
                 ->description('Sincronizar tarefas com a nuvem');

        // Verificar prazos novamente às 14h para tarefas que vencem no mesmo dia
        $schedule->command('tasks:check-deadlines --days=1 --notify')
                 ->dailyAt('14:00')
                 ->description('Verificar tarefas que vencem hoje');

        // Backup automático diário às 23h
        $schedule->command('tasks:sync-cloud --user-id=all')
                 ->dailyAt('23:00')
                 ->description('Backup automático de todas as tarefas');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 