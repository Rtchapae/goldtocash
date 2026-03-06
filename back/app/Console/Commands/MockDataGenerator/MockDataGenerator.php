<?php

namespace App\Console\Commands\MockDataGenerator;

use Exception;
use Illuminate\Console\Command;

abstract class MockDataGenerator extends Command
{
    public static function signature(): string
    {
        $class = get_called_class();
        return explode(' ', (new $class)->signature)[0];
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        if (app()->environment() != 'local') {
            throw new Exception('cannot run unless in local env');
        }
    }

    public function randomString(int $length, string $overrideChars = null): string
    {
        $chars = str_split('abcdefghijklmnopqrstuvwxyz');
        $str = '';
        while (strlen($str) < $length) {
            $x = $overrideChars ? str_split($overrideChars) : $chars;
            $str .= $x[array_rand($x)];
        }
        return $str;
    }

    public function randomInt(int $length): int
    {
        $number = $this->randomString($length, '0123456789');

        if ($number[0] === '0' && $length > 1) {
            $number .= $this->randomInt(1);
        }

        return (int) $number;
    }

    public function randomAlphaNum(int $length): string
    {
        return $this->randomString($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    }
}

