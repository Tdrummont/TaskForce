<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class HolidayService
{
    private const CACHE_PREFIX = 'holidays_';
    private const CACHE_TTL = 86400; // 24 horas

    /**
     * Verifica se uma data específica é feriado para a UF.
     */
    public function isHoliday(\DateTimeInterface $date, string $state): ?array
    {
        try {
            $year = (int) $date->format('Y');
            $list = $this->getHolidays($year, $state);

            $target = (new \Carbon\CarbonImmutable($date))->toDateString();

            $found = collect($list)->firstWhere('date', $target);

            // Retorna ['date' => 'YYYY-MM-DD', 'name' => '...'] ou null
            return $found ?: null;
        } catch (\Exception $e) {
            Log::error('Erro ao verificar feriado específico', [
                'error' => $e->getMessage(),
                'date' => $date->format('Y-m-d'),
                'state' => $state
            ]);
            return null;
        }
    }

    /**
     * Obtém lista de feriados para um ano e estado específicos.
     */
    public function getHolidays(int $year, string $state): array
    {
        $cacheKey = self::CACHE_PREFIX . "{$year}_{$state}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year, $state) {
            // Feriados nacionais fixos
            $nationalHolidays = [
                ['date' => "{$year}-01-01", 'name' => 'Confraternização Universal'],
                ['date' => "{$year}-04-21", 'name' => 'Tiradentes'],
                ['date' => "{$year}-05-01", 'name' => 'Dia do Trabalhador'],
                ['date' => "{$year}-09-07", 'name' => 'Independência do Brasil'],
                ['date' => "{$year}-10-12", 'name' => 'Nossa Senhora Aparecida'],
                ['date' => "{$year}-11-02", 'name' => 'Finados'],
                ['date' => "{$year}-11-15", 'name' => 'Proclamação da República'],
                ['date' => "{$year}-12-25", 'name' => 'Natal'],
            ];

            try {
                $response = Http::timeout(5)->get('https://api.invertexto.com/v1/holidays', [
                    'token' => config('services.invertexto.token'),
                    'state' => $state,
                    'year' => $year,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $apiHolidays = $data['holidays'] ?? [];
                    // Combinar feriados nacionais com os da API
                    return array_merge($nationalHolidays, $apiHolidays);
                }

                Log::warning('Falha ao obter feriados da API, usando feriados nacionais', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return $nationalHolidays;
            } catch (\Exception $e) {
                Log::warning('Erro ao consultar API de feriados, usando feriados nacionais', [
                    'error' => $e->getMessage(),
                    'year' => $year,
                    'state' => $state
                ]);

                return $nationalHolidays;
            }
        });
    }
}
