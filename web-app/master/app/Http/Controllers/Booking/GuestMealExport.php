<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Models\Booking\GuestMeals;

class GuestMealExport extends Controller
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

        return GuestMeals::leftJoin("meals", "meals.id", "guest_meals.meal_id")
        //di na guest_meals.booking_id name ng column but rather guest_id
        ->leftJoin("guests", "guests.id", "guest_meals.guest_id")
        ->leftJOin("bookings","bookings.reference_number","guests.booking_reference_number")
        ->select(
            "guest_meals.id as guest_meal_id",
            "guests.booking_reference_number as booking_reference",
            "guests.reference_number as guest_reference_number",
            DB::raw('CONCAT(guests.first_name, " ", guests.last_name) as guests_name'),
            "guests.age",
            "meals.name as meal_name",
            "meals.type as meal_type",
            "guest_meals.remaining",
            "bookings.type as booking_type",
            DB::raw('(CASE WHEN guest_meals.remaining > 0 THEN "Unclaimed" ELSE "Claimed" END) as status')
        )->where("guests.age",">",2)
        ->whereDate('bookings.start_datetime', '<=', $end_date)
        ->whereDate('bookings.start_datetime', '>=', $start_date)
        ->orderBy("bookings.reference_number")
        ->get();
    }
}
