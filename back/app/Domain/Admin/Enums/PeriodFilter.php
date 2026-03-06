<?php

namespace App\Domain\Admin\Enums;

enum PeriodFilter: string
{
    case ALL = 'all';
    case TODAY = 'today';
    case YESTERDAY = 'yesterday';
    case CURRENT_MONTH = 'currentmonth';
    case LAST_MONTH = 'lastmonth';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'All',
            self::TODAY => 'Today',
            self::YESTERDAY => 'Yesterday',
            self::CURRENT_MONTH => 'Current Month',
            self::LAST_MONTH => 'Last Month',
            self::CUSTOM => 'Custom',
        };
    }

    public static function default(): self
    {
        return self::CURRENT_MONTH;
    }
}

