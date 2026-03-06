<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\DTOs\SentMessageDto;
use App\Domain\Sms\Models\SentMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentSentMessageRepository implements SentMessageRepositoryInterface
{
    public function storeDto(SentMessageDto $dto): ?SentMessage
    {
        return SentMessage::storeDto($dto);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = SentMessage::query();

        if (!empty($filters['from'])) {
            $query->where('from', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('to', $filters['to']);
        }

        if (!empty($filters['mid'])) {
            $query->where('mid', $filters['mid']);
        }

        $perPage = $filters['per_page'] ?? 20;
        $page = $filters['page'] ?? 1;

        $results = $query->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

        // Attach receipts to each message
        $results->getCollection()->transform(function (SentMessage $msg) {
            $msg->load('receipts');
            return $msg;
        });

        return $results;
    }

    public function findWithReceipts(int $id): ?SentMessage
    {
        return SentMessage::with('receipts')->find($id);
    }
}

