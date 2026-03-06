<?php

namespace App\Domain\Orders\Dto;

final class FedExLocationDto
{
    public function __construct(
        private readonly ?float $distanceInMiles,
        private readonly ?string $companyName,
        private readonly ?string $displayName,
        private readonly string $streetLines,
        private readonly string $city,
        private readonly string $state,
        private readonly string $postalCode,
        private readonly string $countryCode,
    ) {
    }

    public function getDistanceInMiles(): ?float
    {
        return $this->distanceInMiles;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->streetLines,
            $this->city,
            $this->state . ' ' . $this->postalCode,
            $this->countryCode,
        ]);

        return implode(', ', $parts);
    }
}
