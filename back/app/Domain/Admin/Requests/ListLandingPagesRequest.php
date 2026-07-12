<?php

namespace App\Domain\Admin\Requests;

use App\Domain\LandingPages\Support\LandingPagePathNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListLandingPagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function expectsJson(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
            'order-by' => ['nullable', 'string', Rule::in(['id', 'title', 'path', 'active', 'created_at'])],
            'order-dir' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function page(): int
    {
        return (int) ($this->input('page') ?: 1);
    }

    public function perPage(): int
    {
        return (int) ($this->input('per_page') ?: 15);
    }

    public function search(): ?string
    {
        $search = $this->input('search');

        return $search !== null && $search !== '' ? (string) $search : null;
    }

    public function orderBy(): ?string
    {
        return $this->input('order-by');
    }

    public function orderDir(): string
    {
        return $this->input('order-dir') ?: 'desc';
    }
}
