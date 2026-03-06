<?php

namespace App\Domain\Sms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsOptout extends Model
{
    protected $table = 'sms_optouts';

    protected $fillable = [
        'phone',
    ];
}

