<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class HolidayService
{
    private string $base;
    private string $token;

    public function __construct()
    {
        $this->base  = rtrim(config('services.invertexto.base', env('INVERTEXTO_BASE', 'https://api.invertexto.com')), '/');
        $this->token = config('services.invertexto.token', env('INVERTEXTO_TOKEN'));
    }

    /**
     * Retorna lista de feriados (array) p/ ano + UF (opcional), com cache.
     */
    public function getHolidays(int $year, ?string $state = null): array
    {
        $state = $state ? Str::upper($state) : null;
        $cacheKey = "holidays:{$year}:" . ($state ?: 'ALL');

        // cache 24h (ajuste se quiser)
        return Cache::remember($cacheKey, now()->addDay(), function () use ($year, $state) {
            $endpoint = "{$this->base}/v1/holidays/{$year}";

            $resp = Http::timeout(10)
                ->retry(2, 300)
                ->withToken($this->token) // envia como Bearer
                ->get($endpoint, array_filter([
                    'state' => $state, // SP, RJ, etc
                ]));

            if (!$resp->ok()) {
                // Loga e retorna vazio pra não quebrar o fluxo
                logger()->warning('Invertexto holidays API error', [
                    'status' => $resp->status(),
                    'body'   => $resp->body(),
                ]);
                return [];
            }

            $json = $resp->json();
            // A API retorna array de objetos com "date", "name", "type", etc.
            return is_array($json) ? $json : [];
        });
    }

    /**
     * Verifica se uma data específica é feriado para a UF.
     */
    public function isHoliday(\DateTimeInterface $date, string $state): ?array
    {
        $year = (int) $date->format('Y');
        $list = $this->getHolidays($year, $state);

        $target = (new \Carbon\CarbonImmutable($date))->toDateString();

        $found = collect($list)->firstWhere('date', $target);

        // Retorna ['date' => 'YYYY-MM-DD', 'name' => '...'] ou null
        return $found ?: null;
    }

    /**
     * Verifica se uma data (Y-m-d) é feriado para uma UF (opcional).
     */
    public function isHolidayByDate(string $dateYmd, ?string $state = null): ?array
    {
        $date = Carbon::parse($dateYmd);
        $list = $this->getHolidays((int)$date->format('Y'), $state);

        foreach ($list as $h) {
            if (!empty($h['date']) && Carbon::parse($h['date'])->isSameDay($date)) {
                return $h; // retorna o objeto do feriado
            }
        }
        return null;
    }
}
