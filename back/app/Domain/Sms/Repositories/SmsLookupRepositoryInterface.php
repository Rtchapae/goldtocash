<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\Models\SmsLookup;

interface SmsLookupRepositoryInterface
{
    public function findByPhone(string $phone): ?SmsLookup;

    public function findNonAssumedByPhone(string $phone): ?SmsLookup;
}


