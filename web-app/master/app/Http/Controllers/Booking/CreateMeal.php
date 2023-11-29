<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking\Meals;
use Carbon\Carbon;

class CreateMeal extends Controller
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
        // return $request->all();

        // Check if stub type exist
        $meal = Meals::where('name', $request->name)->first();

        if ($meal) {
            return response()->json(['error'=>'MEAL_ALREADY_EXISTS', 'message'=>'Meal type already exist'], 400);
        }

        $newMeals = Meals::create([
            'name' => $request->Meal,
            'type' => $request->type,
            'available_from' => Carbon::parse($request->available_from)->setTimezone('Asia/Manila')->toDateString(),
            'available_to' => Carbon::parse($request->available_to)->setTimezone('Asia/Manila')->toDateString(),
            'description' => $request->description,
            'created_at' =>  Carbon::now()->setTimezone('Asia/Manila')->toDateTimeString(),
            'updated_at' => null,
            'status' => 1
        ]);

        if (!$newMeals) {
            return response()->json(['error'=>'FAILED_TO_CREATE_MEAL', 'message'=>'Failed to create Meal.'], 400);
        }

        // return response()->json(['statys'=>'OK', 'message'=>'Added new stub.'], 200);
        return response()->json($newMeals, 200);
    }
}
