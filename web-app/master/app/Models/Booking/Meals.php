<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meals extends Model
{
    use SoftDeletes;
    //
    protected $connection = 'camaya_booking_db';
    protected $table = 'meals';

    protected $fillable = [
        'name',
        'type',
        'available_from',
        'available_to',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'deleted_by'

    ];

}
