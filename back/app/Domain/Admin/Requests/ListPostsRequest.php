<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListPostsRequest extends FormRequest
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
            'period' => ['nullable', 'string'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'order-by' => ['nullable', 'string', 'in:id,title,created_at,active'],
            'order-dir' => ['nullable', 'string', 'in:asc,desc'],
            'search' => ['nullable', 'string', 'max:255'],
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

    public function period(): ?string
    {
        return $this->input('period');
    }

    public function from(): ?string
    {
        return $this->input('from');
    }

    public function to(): ?string
    {
        return $this->input('to');
    }

    public function orderBy(): ?string
    {
        return $this->input('order-by');
    }

    public function orderDir(): ?string
    {
        return $this->input('order-dir', 'desc');
    }

    public function search(): ?string
    {
        $v = $this->input('search');
        if (! is_string($v)) {
            return null;
        }
        $t = trim($v);

        return $t === '' ? null : $t;
    }
}

