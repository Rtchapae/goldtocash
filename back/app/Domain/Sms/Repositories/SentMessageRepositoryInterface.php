<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\DTOs\SentMessageDto;
use App\Domain\Sms\Models\SentMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SentMessageRepositoryInterface
{
    public function storeDto(SentMessageDto $dto): ?SentMessage;

    public function search(array $filters): LengthAwarePaginator;

    public function findWithReceipts(int $id): ?SentMessage;
}

