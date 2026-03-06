<?php

namespace App\Domain\Sms\Models;

use App\Domain\Sms\DTOs\SentMessageDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SentMessage extends Model
{
    protected $table = 'sent_messages';

    protected $fillable = [
        'provider_name',
        'mid',
        'from',
        'to',
        'message',
        'status',
        'details',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function receipts(): HasMany
    {
        return $this->hasMany(SentMessageReceipt::class, 'mid', 'mid');
    }

    public static function storeDto(SentMessageDto $dto): ?self
    {
        $model = self::query()->create([
            'mid' => $dto->getMid() ?: null,
            'provider_name' => $dto->getProviderName() ?: null,
            'from' => $dto->getFrom() ?: null,
            'to' => $dto->getTo() ?: null,
            'message' => $dto->getMessage() ?: null,
            'status' => $dto->getStatus() ?: null,
            'details' => $dto->getDetails() ?: null,
            'submitted_at' => $dto->getSubmittedAt(),
        ]);

        return $model instanceof self ? $model : null;
    }
}

