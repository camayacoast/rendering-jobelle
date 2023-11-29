<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsEvent extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_sms_events';

    protected $fillable = [
        'event_name',
        'recepients',
        'event_description',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];

    public function eventHistory ()
    {
        return $this->hasMany('App\Models\Booking\SmsJobHistory', 'event_id', 'id');
    }
}
