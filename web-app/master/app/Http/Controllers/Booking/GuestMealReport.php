<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Booking\GuestMeals;

class GuestMealReport extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $start_date = $request->start_date ?? Carbon::now()->setTimezone('Asia/Manila');
        $end_date = $request->end_date ?? Carbon::now()->setTimezone('Asia/Manila');

        return GuestMeals::
        leftJoin("meals", "meals.id", "guest_meals.meal_id")
        ->leftJoin("guests","guests.id","guest_meals.guest_id")
        ->leftJoin("bookings", "bookings.reference_number", "guests.booking_reference_number")
        ->select(
            "meals.type as meal_type",
            DB::raw('SUM(guest_meals.remaining) as total_remaining'),
        )->groupBy("meals.type")
        ->whereDate('bookings.start_datetime', '<=', $end_date)
        ->whereDate('bookings.start_datetime', '>=', $start_date)
        ->get();
    }
}
