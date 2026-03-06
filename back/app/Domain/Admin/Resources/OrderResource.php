<?php

namespace App\Domain\Admin\Resources;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderType;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = $this->user;
        $trace = $user->traces()->orderByDesc('created_at')->first();
        $sourceData = $trace?->source ?? [];

        if (!is_array($sourceData)) {
            $sourceData = [];
        }

        $sourceBadges = [];
        if (isset($sourceData['utm_term'])) {
            $sourceBadges[] = ['key' => 'utm_term', 'value' => $sourceData['utm_term']];
        }
        if (isset($sourceData['utm_medium'])) {
            $sourceBadges[] = ['key' => 'utm_medium', 'value' => $sourceData['utm_medium']];
        }
        if (isset($sourceData['utm_source'])) {
            $sourceBadges[] = ['key' => 'utm_source', 'value' => $sourceData['utm_source']];
        }
        if (isset($sourceData['utm_campaign'])) {
            $sourceBadges[] = ['key' => 'utm_campaign', 'value' => $sourceData['utm_campaign']];
        }

        $url = $this->submission_url ? parse_url($this->submission_url, PHP_URL_PATH) : '';

        $amount = $this->amount !== null && $this->amount !== '' ? number_format((float) $this->amount, 2) : 'None';

        $statusText = $this->getStatusText();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $user->name ?? '',
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email ?? '',
            'phone' => $user->phone ?? '',
            'phone_verified' => $user->verify ?? false,
            'address' => $user->address ?? '',
            'city' => $user->city ?? '',
            'state' => $user->state ?? '',
            'zip' => $user->zip ?? '',
            'order_number' => $this->id,
            'url' => $url,
            'source_badges' => $sourceBadges,
            'amount' => $amount,
            'amount_raw' => $this->amount !== null && $this->amount !== '' ? (float) $this->amount : null,
            'status' => $statusText,
            'status_value' => (int) $this->status,
            'order_type' => $this->order_type ?: 'online',
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch?->display_name ?? null,
            'date_created' => $this->created_at?->format('M j, Y H:i'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'is_returning_customer' => $this->is_returning_customer ?? false,
        ];
    }

    private function getStatusText(): string
    {
        try {
            $statusEnum = OrderStatus::from($this->status);
            return $statusEnum->label();
        } catch (\ValueError $e) {
            return 'Unknown';
        }
    }

    private function getOrderTypeText(): string
    {
        if (!$this->order_type) {
            return OrderType::ONLINE->label();
        }

        try {
            $typeEnum = OrderType::from($this->order_type);
            return $typeEnum->label();
        } catch (\ValueError $e) {
            return OrderType::ONLINE->label();
        }
    }
}

