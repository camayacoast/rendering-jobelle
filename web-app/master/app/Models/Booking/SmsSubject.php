<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class SmsSubject extends Model
{
    protected $table = 'tbl_sms_subject';

    protected $fillable = [
        'subject',
    ];

}
