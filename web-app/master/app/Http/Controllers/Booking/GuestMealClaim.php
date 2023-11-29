<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\Booking\GuestMeals;

use Carbon\Carbon;

class GuestMealClaim extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $guest_meal = GuestMeals::leftJoin("meals", "meals.id", "guest_meals.meal_id")
        //di na guest_meals.booking_id name ng column but rather guest_id
        ->leftJoin("guests", "guests.id", "guest_meals.guest_id")
        ->leftJoin("bookings","bookings.reference_number","guests.booking_reference_number")
        ->select(
            "guest_meals.id",
            "guest_meals.remaining",
            "bookings.reference_number"
        )
        ->where('guests.reference_number',$request->booking_ref)
        ->where('meals.type',$request->type)
        ->first();

        if (!$guest_meal) {
            return response()->json(['error' => 'MEAL_NOT_FOUND', 'message' => 'Incorrect Guest Ref/Meal Type'], 404);
        }

        $scan_meal = GuestMeals::find($guest_meal->id);

        if (!$scan_meal) {
            return response()->json(['error' => 'MEAL_NOT_FOUND', 'message' => 'Invalid Meal Type.'], 404);
        }

        $count = $guest_meal->remaining;
        $total = $count - 1;


        if($total < 0) {
            return response()->json(['error' => 'MEAL_NOT_FOUND', 'message' => 'There are no Meal Available.'], 404);
        }
        else {

            $scan_meal->update([
                'remaining' => $total,
            ]);

            return response()->json(['message' => 'Meal Claimed.'], 200);

        }
    }
}
