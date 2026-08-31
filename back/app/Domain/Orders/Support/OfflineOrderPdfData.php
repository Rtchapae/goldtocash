<?php

namespace App\Domain\Orders\Support;

use App\Domain\Orders\Models\Order;
use App\Domain\Users\Models\User;
use Carbon\Carbon;

class OfflineOrderPdfData
{
    /**
     * @return array<string, mixed>
     */
    public static function forOrder(Order $order, User $user, string $buyerName = ''): array
    {
        $params = $user->government_id_params;
        if (is_string($params) && $params !== '') {
            $decoded = json_decode($params, true);
            $params = is_array($decoded) ? $decoded : [];
        } elseif (! is_array($params)) {
            $params = [];
        }

        return [
            'order' => $order,
            'user' => $user,
            'buyerName' => $buyerName,
            'isPortlandBranch' => $order->branch && strtolower($order->branch->name) === 'portland',
            'dateOfBirth' => self::formatDateOfBirth($user->date_of_birth),
            'govIdNumber' => $params['idNumber'] ?? $params['id_number'] ?? '',
            'stateIssued' => $params['issuer'] ?? '',
            'items' => self::parseItems($order->notes),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function parseItems(?string $notes): array
    {
        if ($notes === null || trim($notes) === '') {
            return [];
        }

        $decoded = json_decode($notes, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map(
                static fn ($item) => trim((string) $item),
                $decoded
            )));
        }

        return array_values(array_filter(array_map(
            static fn ($line) => trim($line),
            preg_split('/\r\n|\r|\n/', $notes) ?: []
        )));
    }

    public static function encodeItems(array $items): ?string
    {
        $clean = array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item),
            $items
        )));

        if ($clean === []) {
            return null;
        }

        return json_encode($clean, JSON_UNESCAPED_UNICODE);
    }

    public static function formatDateOfBirth(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return Carbon::parse($value)->format('m/d/Y');
        } catch (\Throwable) {
            return '';
        }
    }
}
