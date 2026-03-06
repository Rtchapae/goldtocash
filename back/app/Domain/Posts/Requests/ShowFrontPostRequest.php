<?php

namespace App\Domain\Posts\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowFrontPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path_prefix' => ['nullable', 'string'],
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
}

