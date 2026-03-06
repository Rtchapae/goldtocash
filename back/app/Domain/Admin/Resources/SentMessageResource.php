<?php

namespace App\Domain\Admin\Resources;

use App\Domain\Sms\Models\SentMessage;
use Illuminate\Http\Resources\Json\JsonResource;

class SentMessageResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var SentMessage $this */
        return [
            'id' => $this->id,
            'provider_name' => $this->provider_name,
            'mid' => $this->mid,
            'from' => $this->from,
            'to' => $this->to,
            'message' => $this->message,
            'status' => $this->status,
            'details' => $this->details,
            'submitted_at' => $this->submitted_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'cache' => [
                'receipts' => $this->whenLoaded('receipts', function () {
                    return $this->receipts->map(function ($receipt) {
                        return [
                            'id' => $receipt->id,
                            'mid' => $receipt->mid,
                            'status' => $receipt->status,
                            'created_at' => $receipt->created_at?->toDateTimeString(),
                        ];
                    });
                }, []),
            ],
        ];
    }
}

