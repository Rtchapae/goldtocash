<?php

namespace App\Domain\Seo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSeoPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'route_name' => ['required', 'string', 'max:255', 'unique:seo_pages,route_name'],
            'page_url' => ['required', 'string', 'max:255', 'unique:seo_pages,page_url'],
            'page_title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'array'],
            'meta_keywords.*' => ['string', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }
}
