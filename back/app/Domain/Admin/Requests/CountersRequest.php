<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'period' => ['sometimes', 'string', 'in:today,yesterday,currentmonth,lastmonth,all,custom'],
            'from' => ['sometimes', 'nullable', 'date', 'required_with:period'],
            'to' => ['sometimes', 'nullable', 'date', 'required_with:period'],
        ];
    }

    public function period(): string
    {
        return $this->input('period', 'currentmonth');
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

