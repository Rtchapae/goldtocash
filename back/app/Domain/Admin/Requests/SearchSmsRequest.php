<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchSmsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'string', 'max:20'],
            'to' => ['nullable', 'string', 'max:20'],
            'mid' => ['nullable', 'string', 'max:200'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->input($key, $default);
    }
}

