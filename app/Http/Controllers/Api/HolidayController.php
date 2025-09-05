<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HolidayCheckService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    public function __construct(
        private HolidayCheckService $holidayCheckService
    ) {}

    /**
     * Verifica se uma data é feriado (método principal usado pelo frontend)
     */
    public function check(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'state' => 'nullable|string|size:2'
            ]);

            $date = $request->input('date');
            $state = $request->input('state');

            $holidayInfo = $this->holidayCheckService->getHolidayInfo($date, $state);

            return response()->json([
                'is_holiday' => $holidayInfo !== null,
                'holiday' => $holidayInfo
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar feriado', [
                'error' => $e->getMessage(),
                'date' => $request->input('date'),
                'state' => $request->input('state')
            ]);

            return response()->json([
                'is_holiday' => false,
                'holiday' => null,
                'error' => 'Erro ao verificar feriado'
            ], 500);
        }
    }

    /**
     * Verifica múltiplas datas de uma vez
     */
    public function checkMultipleDates(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'dates' => 'required|array|min:1|max:10',
                'dates.*' => 'required|date',
                'state' => 'nullable|string|size:2'
            ]);

            $dates = $request->input('dates');
            $state = $request->input('state');
            $results = [];

            foreach ($dates as $date) {
                $holidayInfo = $this->holidayCheckService->getHolidayInfo($date, $state);
                $results[$date] = [
                    'is_holiday' => $holidayInfo !== null,
                    'holiday' => $holidayInfo
                ];
            }

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar múltiplas datas de feriado', [
                'error' => $e->getMessage(),
                'dates' => $request->input('dates'),
                'state' => $request->input('state')
            ]);

            return response()->json([
                'error' => 'Erro ao verificar feriados'
            ], 500);
        }
    }
}
