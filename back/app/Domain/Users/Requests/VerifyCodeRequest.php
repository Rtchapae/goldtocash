<?php

namespace App\Domain\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
            'code' => ['required', 'string'],
        ];
    }

    public function phone(): string
    {
        return $this->input('phone');
    }

    public function code(): string
    {
        return $this->input('code');
    }
}
