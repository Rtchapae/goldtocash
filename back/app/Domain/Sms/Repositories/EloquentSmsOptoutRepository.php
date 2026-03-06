<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\Models\SmsOptout;
use Illuminate\Contracts\Container\Container;

class EloquentSmsOptoutRepository implements SmsOptoutRepositoryInterface
{
    public function __construct(
        private Container $container
    ) {
    }

    public function hasOptout(string $phone): bool
    {
        return SmsOptout::query()->where('phone', $phone)->exists();
    }

    public function record(string $phone): void
    {
        if ($this->hasOptout($phone)) {
            return;
        }

        SmsOptout::query()->create([
            'phone' => $phone,
            'created_at' => now(),
        ]);

        $this->container->make(SmsOptinRepositoryInterface::class)->remove($phone);
    }

    public function remove(string $phone): void
    {
        SmsOptout::query()->where('phone', $phone)->delete();
    }
}

