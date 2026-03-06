<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminUserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'email' => ['sometimes', 'email', 'unique:users,email,' . $userId],
            'password' => ['sometimes', 'string', 'min:8'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'role' => ['sometimes', 'string', 'in:admin,manager'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}

