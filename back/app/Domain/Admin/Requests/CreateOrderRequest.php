<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'order_type' => ['sometimes', 'string', 'in:online,offline'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'payment_method' => ['sometimes', 'string', 'in:cash,card,check,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // For offline orders without user_id, user_data is required
        if ($this->input('order_type') === 'offline' && !$this->input('user_id')) {
            $rules['user_data'] = ['required', 'array'];
            $rules['user_data.name'] = ['required', 'string', 'max:255'];
            $rules['user_data.phone'] = ['required', 'string', 'max:20'];
            $rules['user_data.address'] = ['required', 'string', 'max:255'];
            $rules['user_data.city'] = ['required', 'string', 'max:255'];
            $rules['user_data.state'] = ['required', 'string', 'max:2'];
            $rules['user_data.zip'] = ['required', 'string', 'max:10'];
            $rules['user_data.country'] = ['sometimes', 'string', 'max:100'];
            $rules['user_data.email'] = ['nullable', 'email', 'max:255'];
            $rules['user_data.first_name'] = ['sometimes', 'string', 'max:255'];
            $rules['user_data.last_name'] = ['sometimes', 'string', 'max:255'];
        }

        return $rules;
    }
}

