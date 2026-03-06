<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryCampaignStatsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source' => ['required', 'string'],
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date'],
        ];
    }

    public function source(): string
    {
        return $this->input('source');
    }

    public function dateFrom(): string
    {
        return $this->input('dateFrom');
    }

    public function dateTo(): string
    {
        return $this->input('dateTo');
    }
}

