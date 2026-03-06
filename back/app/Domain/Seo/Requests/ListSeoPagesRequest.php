<?php

namespace App\Domain\Seo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListSeoPagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 15);
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    public function search(): ?string
    {
        return $this->input('search');
    }
}
