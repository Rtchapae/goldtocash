<?php

namespace App\Domain\Sms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsInbound extends Model
{
    protected $table = 'sms_inbounds';

    protected $fillable = [
        'from',
        'to',
        'message',
        'considered_optout',
    ];

    protected $casts = [
        'considered_optout' => 'boolean',
    ];
}

