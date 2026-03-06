<?php

namespace App\Domain\Sms\Models;

use App\Domain\Sms\DTOs\PhoneLookupDto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLookup extends Model
{
    use HasFactory;

    protected $table = 'sms_lookups';

    protected $fillable = [
        'phone',
        'is_mobile',
        'is_assumed',
        'assumption_reason',
    ];

    protected $casts = [
        'is_mobile' => 'boolean',
        'is_assumed' => 'boolean',
    ];

    public static function storeDto(PhoneLookupDto $dto): ?self
    {
        if (!$dto->needsToBeStored()) {
            return self::instantiateFromDto($dto);
        }

        $existing = self::query()->where('phone', $dto->getPhone())->first();

        if ($existing && !$existing->is_assumed && $dto->getIsAssumed()) {
            return $existing;
        }

        $model = self::query()->updateOrCreate(
            ['phone' => $dto->getPhone()],
            [
                'is_mobile' => $dto->getIsMobile(),
                'is_assumed' => $dto->getIsAssumed(),
                'assumption_reason' => $dto->getAssumptionReason() ?: null,
                'updated_at' => now(),
            ]
        );

        return $model instanceof self ? $model : null;
    }

    public static function instantiateFromDto(PhoneLookupDto $dto): self
    {
        return new self([
            'phone' => $dto->getPhone(),
            'is_mobile' => $dto->getIsMobile(),
            'is_assumed' => $dto->getIsAssumed(),
            'assumption_reason' => $dto->getAssumptionReason() ?: null,
        ]);
    }
}


