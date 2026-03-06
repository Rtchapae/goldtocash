<?php

namespace App\Domain\CustomerIo\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerIoLead extends Model
{
    protected $table = 'customerio_leads';

    protected $fillable = [
        'email',
        'last_transmission_at',
    ];

    protected $casts = [
        'last_transmission_at' => 'datetime',
    ];

    public static function recordLastTransmission(string $email): void
    {
        self::query()->updateOrCreate(
            ['email' => $email],
            ['last_transmission_at' => now()]
        );
    }
}
