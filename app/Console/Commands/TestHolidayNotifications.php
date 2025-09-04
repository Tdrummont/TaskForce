<?php

namespace App\Console\Commands;

use App\Services\HolidayService;
use Illuminate\Console\Command;

class TestHolidayNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:holidays {date?} {state?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o sistema de notificações de feriados';

    /**
     * Execute the console command.
     */
    public function handle(HolidayService $holidayService)
    {
        $date = $this->argument('date') ?: now()->format('Y-m-d');
        $state = $this->argument('state') ?: 'PA';

        $this->info("🔍 Testando verificação de feriados...");
        $this->info("📅 Data: {$date}");
        $this->info("🏛️ Estado: {$state}");
        $this->newLine();

        try {
            // Verificar se é feriado
            $holiday = $holidayService->isHolidayByDate($date, $state);

            if ($holiday) {
                $this->info("🎉 FERIADO DETECTADO!");
                $this->info("📋 Nome: {$holiday['name']}");
                $this->info("📅 Data: {$holiday['date']}");
                $this->info("🏷️ Tipo: {$holiday['type']}");
                $this->info("🌍 Nível: {$holiday['level']}");
                
                $this->newLine();
                $this->info("✅ O sistema deve mostrar um snackbar com esta informação!");
                
            } else {
                $this->info("📅 Data normal - não é feriado");
                $this->info("ℹ️ Nenhum snackbar será exibido");
            }

        } catch (\Exception $e) {
            $this->error("❌ Erro ao verificar feriado: " . $e->getMessage());
        }

        $this->newLine();
        $this->info("🧪 Testando alguns feriados conhecidos...");
        
        $testDates = [
            '2025-12-25' => 'Natal',
            '2025-01-01' => 'Ano Novo',
            '2025-04-21' => 'Tiradentes',
            '2025-09-07' => 'Independência',
            '2025-10-12' => 'Nossa Senhora Aparecida',
        ];

        foreach ($testDates as $testDate => $expectedName) {
            $holiday = $holidayService->isHolidayByDate($testDate, $state);
            
            if ($holiday) {
                $this->info("✅ {$testDate} - {$holiday['name']} ({$holiday['type']})");
            } else {
                $this->warn("❌ {$testDate} - Não detectado como feriado");
            }
        }

        $this->newLine();
        $this->info("🎯 Para testar no frontend:");
        $this->info("1. Acesse o sistema no navegador");
        $this->info("2. Se hoje for feriado, um snackbar aparecerá automaticamente");
        $this->info("3. Use o componente HolidayDateInput para testar outras datas");
        $this->info("4. Acesse /holiday-demo para ver a demonstração completa");
    }
}
