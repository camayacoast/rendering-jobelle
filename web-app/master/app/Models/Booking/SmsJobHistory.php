<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class SmsJobHistory extends Model
{

    protected $table = 'tbl_sms_job_history';

    protected $fillable = [
        'maker_id',
        'event_id',
        'job_id',
        'status',
        'sending_type',
        'sending_date',
        'event_name',
        'recepients',
        'event_description',
        'message'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
}
