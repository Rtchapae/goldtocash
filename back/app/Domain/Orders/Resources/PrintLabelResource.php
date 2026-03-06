<?php

namespace App\Domain\Orders\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintLabelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resource;

        return [
            'status' => $data['status'],
            'label_url' => $data['label_url'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'error' => $data['error'] ?? null,
        ];
    }
}



