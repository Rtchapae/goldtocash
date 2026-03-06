<?php

namespace App\Domain\Orders\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitRegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resource;

        return [
            'status' => $data['status'] ?? false,
            'message' => $data['message'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'access_token' => $data['access_token'] ?? null,
            'token_type' => $data['token_type'] ?? null,
            'password' => $data['password'] ?? null,
            'requires_login' => $data['requires_login'] ?? false,
            'requires_verification' => $data['requires_verification'] ?? false,
            'kit_sent' => $data['kit_sent'] ?? false,
        ];
    }
}

