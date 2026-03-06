<?php

namespace App\Domain\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateFrontUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('front')->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = Auth::guard('front')->id();

        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:50'],
            'zip' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['nullable', 'string', 'in:check,ach,paypal,cashapp'],
            'payment_method_params' => ['nullable', 'array'],
            'government_id' => ['nullable', 'string', 'in:license,passport,military,state'],
            'government_id_params' => ['nullable', 'array'],
            'date_of_birth' => ['nullable', 'date'],
        ];
    }
}


