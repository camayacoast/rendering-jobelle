<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking\Guest;

class ViewBookingMealsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $guest_meals = Guest::select('meals.type', 'meals.name')
        ->where('guests.booking_reference_number', $request->refno)
        ->leftJoin('guest_meals', "guest_meals.guest_id", "guests.id")
        ->leftJoin('meals', 'meals.id', 'guest_meals.meal_id')
        ->select(
            "meals.name as meal_name",
            "meals.type as meal_type",
        )->get();


    $uniqueMeals = $guest_meals->unique(function ($item) {
        return $item['meal_name'] . $item['meal_type'];
    });

    return $uniqueMeals;
    }
}
