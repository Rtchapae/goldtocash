<?php

namespace App\Domain\Sms\Repositories;

interface SmsOptoutRepositoryInterface
{
    public function hasOptout(string $phone): bool;

    public function record(string $phone): void;

    public function remove(string $phone): void;
}

