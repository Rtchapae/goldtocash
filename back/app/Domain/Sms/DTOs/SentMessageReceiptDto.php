<?php

namespace App\Domain\Sms\DTOs;

class SentMessageReceiptDto
{
    private ?string $mid = null;
    private ?string $status = null;

    public function getMid(): string
    {
        return $this->mid ?? '';
    }

    public function setMid(string $mid): self
    {
        $this->mid = $mid;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status ?? '';
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}

