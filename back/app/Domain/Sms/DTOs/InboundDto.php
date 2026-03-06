<?php

namespace App\Domain\Sms\DTOs;

use App\Domain\Sms\Services\SmsUtils;

class InboundDto
{
    private ?string $from = null;
    private ?string $to = null;
    private ?string $message = null;

    public function getFrom(): string
    {
        return $this->from ?? '';
    }

    public function setFrom(string $from): self
    {
        $this->from = SmsUtils::sanitizeStringAsPhone($from);
        return $this;
    }

    public function getTo(): string
    {
        return $this->to ?? '';
    }

    public function setTo(string $to): self
    {
        $this->to = SmsUtils::sanitizeStringAsPhone($to);
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function isOptout(): bool
    {
        return preg_match('/stop|opt-?out/i', $this->getMessage());
    }

    public function isOptin(): bool
    {
        return preg_match('/start|unstop/i', $this->getMessage());
    }
}

