<?php

namespace App\Domain\Sms\DTOs;

use Carbon\Carbon;

class SentMessageDto
{
    private ?string $providerName = null;
    private ?string $mid = null;
    private ?string $from = null;
    private ?string $to = null;
    private ?string $message = null;
    private ?string $status = null;
    private ?string $details = null;
    private ?Carbon $submittedAt = null;

    public function setProviderName(string $providerName): self
    {
        $this->providerName = $providerName;
        return $this;
    }

    public function getProviderName(): string
    {
        return strtolower($this->providerName ?? '');
    }

    public function setMid(string $mid): self
    {
        $this->mid = $mid;
        return $this;
    }

    public function getMid(): string
    {
        return $this->mid ?? '';
    }

    public function setFrom(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getFrom(): string
    {
        return $this->from ?? '';
    }

    public function setTo(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function getTo(): string
    {
        return $this->to ?? '';
    }

    public function setMessage(string $message): self
    {
        $this->message = $message ?? '';
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status ?? '';
    }

    public function getDetails(): string
    {
        return $this->details ?? '';
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;
        return $this;
    }

    public function setSubmittedAt(Carbon $submittedAt): self
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    public function getSubmittedAt(): ?Carbon
    {
        return $this->submittedAt;
    }
}

