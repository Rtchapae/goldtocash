<?php

namespace App\Domain\Users\DTOs;

class TraceSourceDto
{
    private ?string $source = null;
    private ?string $campaign = null;
    private ?string $medium = null;
    private ?string $content = null;
    private ?string $term = null;

    public static function createFromArray(array $data): self
    {
        $dto = new self();

        if (isset($data['source']) || isset($data['utm_source'])) {
            $dto->setSource($data['source'] ?? $data['utm_source'] ?? null);
        }

        if (isset($data['campaign']) || isset($data['utm_campaign'])) {
            $dto->setCampaign($data['campaign'] ?? $data['utm_campaign'] ?? null);
        }

        if (isset($data['medium']) || isset($data['utm_medium'])) {
            $dto->setMedium($data['medium'] ?? $data['utm_medium'] ?? null);
        }

        if (isset($data['content']) || isset($data['utm_content'])) {
            $dto->setContent($data['content'] ?? $data['utm_content'] ?? null);
        }

        if (isset($data['term']) || isset($data['utm_term'])) {
            $dto->setTerm($data['term'] ?? $data['utm_term'] ?? null);
        }

        return $dto;
    }

    public function getHashMapRepresentation(): array
    {
        $vals = [];

        if ($this->source) {
            $vals['utm_source'] = $this->source;
        }
        if ($this->campaign) {
            $vals['utm_campaign'] = $this->campaign;
        }
        if ($this->medium) {
            $vals['utm_medium'] = $this->medium;
        }
        if ($this->content) {
            $vals['utm_content'] = $this->content;
        }
        if ($this->term) {
            $vals['utm_term'] = $this->term;
        }

        return $vals;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setCampaign(?string $campaign): self
    {
        $this->campaign = $campaign;
        return $this;
    }

    public function getCampaign(): ?string
    {
        return $this->campaign;
    }

    public function setMedium(?string $medium): self
    {
        $this->medium = $medium;
        return $this;
    }

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setTerm(?string $term): self
    {
        $this->term = $term;
        return $this;
    }

    public function getTerm(): ?string
    {
        return $this->term;
    }
}

