<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HolidayCheckService
{
    public function __construct(
        private HolidayService $holidayService
    ) {}

    /**
     * Verifica se uma data é feriado
     */
    public function isHoliday(string $date, ?string $state = null): ?array
    {
        try {
            $carbonDate = Carbon::parse($date);
            $state = $state ?: 'SP'; // Default para SP se não especificado
            
            return $this->holidayService->isHoliday($carbonDate, $state);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar se é feriado', [
                'error' => $e->getMessage(),
                'date' => $date,
                'state' => $state
            ]);
            return null;
        }
    }

    /**
     * Obtém informações completas sobre um feriado
     */
    public function getHolidayInfo(string $date, ?string $state = null): ?array
    {
        return $this->isHoliday($date, $state);
    }
}
