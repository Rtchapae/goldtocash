<?php

namespace App\Domain\Sms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsOptin extends Model
{
    protected $table = 'sms_optins';

    protected $fillable = [
        'phone',
        'method',
    ];
}

