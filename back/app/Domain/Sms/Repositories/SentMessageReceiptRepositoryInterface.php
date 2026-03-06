<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\DTOs\SentMessageReceiptDto;
use App\Domain\Sms\Models\SentMessageReceipt;

interface SentMessageReceiptRepositoryInterface
{
    public function storeDto(SentMessageReceiptDto $dto): ?SentMessageReceipt;
}

