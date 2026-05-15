<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListExpensesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
            'order-by' => ['nullable', 'string', 'in:email,id,name,phone,amount,shipping,created_at,updated_at'],
            'order-dir' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 15);
    }

    public function orderBy(): ?string
    {
        return $this->input('order-by');
    }

    public function orderDir(): string
    {
        return $this->input('order-dir', 'desc');
    }

    public function search(): ?string
    {
        $value = $this->input('search');

        return $value !== null && $value !== '' ? (string) $value : null;
    }
}

