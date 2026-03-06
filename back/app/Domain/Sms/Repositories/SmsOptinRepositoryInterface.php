<?php

namespace App\Domain\Sms\Repositories;

interface SmsOptinRepositoryInterface
{
    public function hasOptin(string $phone): bool;

    public function record(string $phone, ?string $method = null): void;

    public function remove(string $phone): void;
}

