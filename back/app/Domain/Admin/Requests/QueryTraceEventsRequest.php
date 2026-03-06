<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryTraceEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eventType' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'campaign' => ['nullable', 'string'],
            'medium' => ['nullable', 'string'],
            'term' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'dateFrom' => ['nullable', 'date'],
            'dateTo' => ['nullable', 'date', 'after_or_equal:dateFrom'],
        ];
    }

    public function eventType(): ?string
    {
        return $this->input('eventType');
    }

    public function source(): ?string
    {
        return $this->input('source');
    }

    public function campaign(): ?string
    {
        return $this->input('campaign');
    }

    public function medium(): ?string
    {
        return $this->input('medium');
    }

    public function term(): ?string
    {
        return $this->input('term');
    }

    public function content(): ?string
    {
        return $this->input('content');
    }

    public function dateFrom(): ?string
    {
        return $this->input('dateFrom');
    }

    public function dateTo(): ?string
    {
        return $this->input('dateTo');
    }
}

