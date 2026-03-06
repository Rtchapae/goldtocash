<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = [
        'phone',
        'code',
        'active',
        'attempts',
    ];

    public $timestamps = true;
}

