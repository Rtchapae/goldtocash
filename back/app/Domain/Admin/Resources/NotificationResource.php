<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user_name' => $this->data['user_name'] ?? null,
            'user_avatar' => $this->data['user_avatar'] ?? null,
            'order_number' => $this->data['order_number'] ?? null,
            'kit_number' => $this->data['kit_number'] ?? null,
            'amount' => $this->data['amount'] ?? null,
            'status' => $this->data['status'] ?? null,
        ];
    }
}




