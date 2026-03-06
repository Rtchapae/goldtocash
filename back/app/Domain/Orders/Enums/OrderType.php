<?php

namespace App\Domain\Orders\Enums;

enum OrderType: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
        };
    }
}

