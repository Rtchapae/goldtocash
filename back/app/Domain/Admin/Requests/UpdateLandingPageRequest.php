<?php

namespace App\Domain\Admin\Requests;

use App\Domain\LandingPages\Support\LandingPagePathNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLandingPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function expectsJson(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('path')) {
            $this->merge([
                'path' => LandingPagePathNormalizer::normalize($this->input('path')),
            ]);
        }
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'path' => [
                'required',
                'string',
                'max:255',
                'regex:/^\/[a-z0-9\-\/]+$/i',
                Rule::unique('landing_pages', 'path')->ignore($id),
                function ($attribute, $value, $fail) {
                    if (LandingPagePathNormalizer::isReserved($value)) {
                        $fail('This URL path is reserved for the main site.');
                    }
                },
            ],
            'blocks' => ['required', 'array', 'min:1'],
            'blocks.*.id' => ['required', 'string', 'max:64'],
            'blocks.*.type' => ['required', 'string', Rule::in([
                'heading', 'text', 'image', 'text_image', 'cta', 'kit_form', 'gold_calculator', 'spacer', 'divider',
            ])],
            'blocks.*.data' => ['required', 'array'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'active' => ['nullable', 'boolean'],
        ];
    }
}
