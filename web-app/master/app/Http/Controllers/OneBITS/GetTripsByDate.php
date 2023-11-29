<?php

namespace App\Http\Controllers\OneBITS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models\Transportation\Schedule;

class GetTripsByDate extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
        $date = Carbon::parse($request->date)->setTimezone('Asia/Manila')->format('Y-m-d');

        $schedules = Schedule::where('trip_date', $date)
                    ->leftJoin('routes', 'schedules.route_id', '=', 'routes.id')
                    ->leftJoin('locations as origin', 'routes.origin_id', '=', 'origin.id')
                    ->leftJoin('locations as destination', 'routes.destination_id', '=', 'destination.id')
                    ->leftJoin('seat_allocations', 'schedules.id', '=', 'seat_allocations.schedule_id')
                    ->where('seat_allocations.name', '=', '1BITS')
                    ->select(
                        'schedules.*',
                        
                        'origin.code as origin_code',
                        'destination.code as destination_code',
                    )
                    ->get();

        return $schedules;
    }
}
