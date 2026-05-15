<?php

namespace App\Domain\Sms\Services;

class SmsUtils
{
    public static function sanitizeStringAsPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        return preg_replace('/^\+?1?/', '', $phone);
    }

    /**
     * Last 10 digits (US) for comparing stored phones in any format to E.164 (+1…).
     */
    public static function digits10(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';
        if (strlen($digits) >= 11 && str_starts_with($digits, '1')) {
            $digits = substr($digits, -10);
        } elseif (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }

        return strlen($digits) === 10 ? $digits : '';
    }

    public static function isValidPhone(string $phone): bool
    {
        if (strlen($phone) !== 10) {
            return false;
        }

        if (in_array(substr($phone, 0, 1), ['+', '1', '0'])) {
            return false;
        }

        $matches = [];
        preg_match('/\D/', $phone, $matches);
        if (count($matches)) {
            return false;
        }

        return true;
    }

    /**
     * Check if SMS (Twilio) is properly configured
     */
    public static function isSmsConfigured(): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        return !empty($sid) && !empty($token) && !empty($from);
    }
}

