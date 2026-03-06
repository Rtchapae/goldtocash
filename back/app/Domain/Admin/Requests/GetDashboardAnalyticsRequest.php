<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetDashboardAnalyticsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'period' => ['nullable', 'string'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ];
    }

    public function period(): ?string
    {
        return $this->input('period');
    }

    public function from(): ?string
    {
        return $this->input('from');
    }

    public function to(): ?string
    {
        return $this->input('to');
    }
}

