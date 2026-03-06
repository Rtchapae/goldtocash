<?php

namespace App\Domain\Sms\Repositories;

use App\Domain\Sms\Models\SmsOptin;
use Illuminate\Contracts\Container\Container;

class EloquentSmsOptinRepository implements SmsOptinRepositoryInterface
{
    public function __construct(
        private Container $container
    ) {
    }

    public function hasOptin(string $phone): bool
    {
        return SmsOptin::query()->where('phone', $phone)->exists();
    }

    public function record(string $phone, ?string $method = null): void
    {
        if ($this->hasOptin($phone)) {
            return;
        }

        SmsOptin::query()->create([
            'phone' => $phone,
            'method' => $method,
        ]);

        $this->container->make(SmsOptoutRepositoryInterface::class)->remove($phone);
    }

    public function remove(string $phone): void
    {
        SmsOptin::query()->where('phone', $phone)->delete();
    }
}

