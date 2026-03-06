<?php

namespace App\Domain\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'sessions';

    protected $fillable = [
        'ip',
        'user_id',
        'user_agent',
        'times',
    ];

    protected $casts = [
        'times' => 'integer',
    ];
}

