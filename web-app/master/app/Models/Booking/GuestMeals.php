<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestMeals extends Model
{
    use SoftDeletes;
    //
    protected $connection = 'camaya_booking_db';
    protected $table = 'guest_meals';

    protected $fillable = [
        'guest_id',
        'meal_id',
        'remaining',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'deleted_by'

    ];

}
