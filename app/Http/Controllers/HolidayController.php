<?php

namespace App\Http\Controllers;

use App\Services\HolidayService;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function check(Request $request, HolidayService $holidays)
    {
        $request->validate([
            'date'  => ['required','date'], // Y-m-d
            'state' => ['nullable','string','size:2'],
        ]);

        $hit = $holidays->isHoliday(\Carbon\Carbon::parse($request->date), $request->state ?: 'SP');

        return response()->json([
            'is_holiday' => (bool)$hit,
            'holiday'    => $hit, // null ou objeto {date,name,type,...}
        ]);
    }
}
