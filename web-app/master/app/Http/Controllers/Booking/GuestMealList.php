<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Booking\GuestMeals;

class GuestMealList extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        return GuestMeals::leftJoin("meals", "meals.id", "guest_meals.meal_id")
        //di na guest_meals.booking_id name ng column but rather guest_id
        ->leftJoin("guests", "guests.id", "guest_meals.guest_id")
        ->leftJoin("bookings", "bookings.reference_number", "guests.booking_reference_number")
        ->select(
            "guests.reference_number as guest_reference_number",
            "guest_meals.id as guest_meal_id",
            "guests.age",
            "bookings.reference_number as booking_reference",
            DB::raw('CONCAT(guests.first_name, " ", guests.last_name) as guests_name'),
            "meals.name as meal_name",
            "meals.type as meal_type",
            "guest_meals.remaining",
            "bookings.type as booking_type",
            DB::raw('(CASE WHEN guest_meals.remaining > 0 THEN "Unclaimed" ELSE "Claimed" END) as status')
        )
        ->get();

        //Original

        // return GuestMeals::leftJoin("bookings", "bookings.id", "guest_meals.booking_id")
        // ->leftJoin("meals", "meals.id", "guest_meals.meal_id")
        // ->leftJoin("guests", "guests.booking_reference_number", "bookings.reference_number")
        // ->select(
        //     "guest_meals.id as guest_meal_id",
        //     "bookings.reference_number",
        //     DB::raw('CONCAT(guests.first_name, " ", guests.last_name) as guests_name'),
        //     "meals.name as meal_name",
        //     "meals.type as meal_type",
        //     "guest_meals.remaining",
        //     "bookings.type as booking_type",
        //     DB::raw('(CASE WHEN guest_meals.remaining > 0 THEN "Unclaimed" ELSE "Claimed" END) as status')
        // )
        // ->get();
    }
}
