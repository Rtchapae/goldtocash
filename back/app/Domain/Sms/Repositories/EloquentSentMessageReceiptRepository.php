<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\DTOs\SentMessageReceiptDto;
use App\Domain\Sms\Models\SentMessageReceipt;

class EloquentSentMessageReceiptRepository implements SentMessageReceiptRepositoryInterface
{
    public function storeDto(SentMessageReceiptDto $dto): ?SentMessageReceipt
    {
        $model = SentMessageReceipt::query()->create([
            'mid' => $dto->getMid(),
            'status' => $dto->getStatus(),
            'created_at' => now(),
        ]);

        return $model instanceof SentMessageReceipt ? $model : null;
    }
}

