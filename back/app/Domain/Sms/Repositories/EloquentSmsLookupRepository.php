<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\Models\SmsLookup;

class EloquentSmsLookupRepository implements SmsLookupRepositoryInterface
{
    public function findByPhone(string $phone): ?SmsLookup
    {
        return SmsLookup::query()->where('phone', $phone)->first();
    }

    public function findNonAssumedByPhone(string $phone): ?SmsLookup
    {
        return SmsLookup::query()
            ->where('phone', $phone)
            ->where('is_assumed', false)
            ->first();
    }
}


