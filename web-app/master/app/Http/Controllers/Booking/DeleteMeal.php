<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking\Meals;

use Carbon\Carbon;

class DeleteMeal extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $mealId)
    {
        // Find the meal by ID
        $meal = Meals::find($mealId);

        // Check if the meal exists
        if (!$meal) {
            return response()->json(['error' => 'MEAL_NOT_FOUND', 'message' => 'Meal not found.'], 404);
        }

        // Update the deleted_at and deleted_by columns
        $meal->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => $request->user()->id
        ]);

        return response()->json(['message' => 'Meal marked as deleted.'], 200);
    }
}
