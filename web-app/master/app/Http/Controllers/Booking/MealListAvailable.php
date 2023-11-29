<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking\Meals;

class MealListAvailable extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return Meals::where('available_from','<=',$request->visit)
        ->where('available_to','>=',$request->depart)
        ->where('deleted_at',null)
        ->get();
    }
}
