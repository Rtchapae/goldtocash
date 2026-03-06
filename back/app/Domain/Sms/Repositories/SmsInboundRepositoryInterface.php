<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\DTOs\InboundDto;
use App\Domain\Sms\Models\SmsInbound;

interface SmsInboundRepositoryInterface
{
    public function storeDto(InboundDto $dto): ?SmsInbound;
}

