<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\DTOs\InboundDto;
use App\Domain\Sms\Models\SmsInbound;

class EloquentSmsInboundRepository implements SmsInboundRepositoryInterface
{
    public function storeDto(InboundDto $dto): ?SmsInbound
    {
        $model = SmsInbound::query()->create([
            'from' => $dto->getFrom(),
            'to' => $dto->getTo(),
            'message' => $dto->getMessage(),
            'considered_optout' => $dto->isOptout(),
            'created_at' => now(),
        ]);

        return $model instanceof SmsInbound ? $model : null;
    }
}

