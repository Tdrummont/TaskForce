<?php

namespace App\Console\Commands;

use App\Services\HolidayService;
use Illuminate\Console\Command;

class PrimeHolidaysCache extends Command
{
    protected $signature = 'holidays:prime {year?} {--state=* : UF(s), ex: --state=SP --state=RJ}';
    protected $description = 'Preenche o cache de feriados para o ano/UF.';

    public function handle(HolidayService $service)
    {
        $year = (int)($this->argument('year') ?? now()->year);
        $states = $this->option('state') ?: ['SP'];

        foreach ($states as $uf) {
            $this->info("Carregando feriados {$year}/{$uf}...");
            $items = $service->getHolidays($year, $uf);
            $this->info("OK: ".count($items)." feriados.");
        }

        return self::SUCCESS;
    }
}
