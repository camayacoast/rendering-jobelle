<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class SmsLogs extends Model
{
    protected $table = 'tbl_sms_logs';

    protected $fillable = [
        'maker_id',
        'message_id',
        'user_id',
        'user',
        'account_id',
        'account',
        'recipient',
        'message',
        'sender_name',
        'network',
        'type',
        'source',
        'event'
    ];

    // protected $casts = [
    //     'created_at' => 'datetime:Y-m-d'
    // ];

}
