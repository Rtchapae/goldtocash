<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'thread_id' => $this['thread_id'],
            'user_id' => $this['user_id'],
            'user_name' => $this['user_name'],
            'user_email' => $this['user_email'],
            'unread_count' => $this['unread_count'],
            'last_message' => $this['last_message'],
            'last_message_date' => $this['last_message_date'],
        ];
    }
}

