<?php

namespace App\Domain\Users\Enums;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case MANAGER = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'User',
            self::ADMIN => 'Admin',
            self::MANAGER => 'Manager',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::USER => 'Regular user (customer)',
            self::ADMIN => 'Administrator with full access',
            self::MANAGER => 'Branch manager',
        };
    }
}

