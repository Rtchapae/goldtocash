<?php

namespace App\Domain\Sms\DTOs;

class PhoneLookupDto
{
    private string $phone = '';
    private bool $isMobile = false;
    private bool $needsToBeStored = true;
    private bool $isAssumed = true;
    private string $assumptionReason = '';

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getIsMobile(): bool
    {
        return $this->isMobile;
    }

    public function setIsMobile(bool $isMobile): self
    {
        $this->isMobile = $isMobile;
        return $this;
    }

    public function needsToBeStored(): bool
    {
        return $this->needsToBeStored;
    }

    public function setNeedsToBeStored(bool $needed): self
    {
        $this->needsToBeStored = $needed;
        return $this;
    }

    public function getIsAssumed(): bool
    {
        return $this->isAssumed;
    }

    public function setIsAssumed(bool $assumed): self
    {
        $this->isAssumed = $assumed;
        return $this;
    }

    public function getAssumptionReason(): string
    {
        return $this->assumptionReason;
    }

    public function setAssumptionReason(string $reason): self
    {
        $this->assumptionReason = $reason;
        return $this;
    }
}


