<?php

namespace App\Domain\Posts\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListFrontPostsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path_prefix' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function pathPrefix(): ?string
    {
        $pathPrefix = $this->input('path_prefix');

        if ($pathPrefix === '' || $pathPrefix === null) {
            return null;
        }

        if (!str_starts_with($pathPrefix, '/')) {
            return '/' . $pathPrefix;
        }

        return $pathPrefix;
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 15);
    }
}

