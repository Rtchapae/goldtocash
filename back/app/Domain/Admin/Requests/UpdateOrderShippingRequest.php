<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function shipping(): float
    {
        return (float) $this->input('shipping');
    }
}

