<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray($request): array
    {
        $statusText = $this->getStatusText($this->order_status);
        $statusBadgeClass = $this->getStatusBadgeClass($this->order_status);
        
        $amount = $this->order_amount ? number_format((float) $this->order_amount, 2) : null;
        $shipping = $this->order_shipping ? number_format((float) $this->order_shipping, 2) : null;
        $total = null;
        if ($this->order_amount || $this->order_shipping) {
            $amountValue = $this->order_amount ? (float) $this->order_amount : 0;
            $shippingValue = $this->order_shipping ? (float) $this->order_shipping : 0;
            $total = number_format($amountValue + $shippingValue, 2);
        }

        return [
            'id' => $this->id,
            'user_id' => $this->id,
            'order_id' => $this->order_id,
            'name' => trim(($this->last_name ?? '') . ' ' . ($this->first_name ?? '')) ?: $this->name,
            'status' => $statusText,
            'status_badge_class' => $statusBadgeClass,
            'amount' => $amount,
            'shipping' => $shipping,
            'total' => $total,
            'order_created_at' => $this->order_created_at ? date('m/d/Y H:i:s', strtotime($this->order_created_at)) : null,
            'order_updated_at' => $this->order_updated_at ? date('m/d/Y H:i:s', strtotime($this->order_updated_at)) : null,
        ];
    }

    private function getStatusText(?int $status): string
    {
        if ($status === null) {
            return 'No Order';
        }

        return match ($status) {
            0 => 'Kit Requested',
            1 => 'In Transit',
            2 => 'Items Received',
            3 => 'Appraising',
            4 => 'Offer Sent',
            5 => 'Offer Accepted',
            6 => 'Paid',
            7 => 'Offer Denied',
            8 => 'Items Sent Back',
            9 => 'No Sale',
            10 => 'Items Sent Back',
            default => 'Unknown',
        };
    }

    private function getStatusBadgeClass(?int $status): string
    {
        if ($status === null) {
            return 'bg-secondary';
        }

        return match ($status) {
            0 => 'bg-warning',
            1 => 'bg-info',
            2 => 'bg-primary',
            3 => 'bg-secondary',
            4 => 'bg-success',
            5 => 'bg-success',
            6 => 'bg-warning',
            7 => 'bg-danger',
            8 => 'bg-info',
            9 => 'bg-secondary',
            10 => 'bg-info',
            default => 'bg-secondary',
        };
    }
}

