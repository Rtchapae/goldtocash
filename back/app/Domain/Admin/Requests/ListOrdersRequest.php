<?php

namespace App\Domain\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListOrdersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:10000'],
            'export' => ['sometimes', 'boolean'],
            'nameQuery' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'utm_campaign' => ['sometimes', 'nullable', 'string', 'max:255'],
            'utm_medium' => ['sometimes', 'nullable', 'string', 'max:255'],
            'period' => ['sometimes', 'string', 'in:today,yesterday,currentmonth,lastmonth,all,custom'],
            'from' => ['sometimes', 'nullable', 'date'],
            'to' => ['sometimes', 'nullable', 'date'],
            'order-by' => ['sometimes', 'nullable', 'string'],
            'order-dir' => ['sometimes', 'string', 'in:asc,desc'],
            'order_type' => ['sometimes', 'nullable', 'string', 'in:online,offline'],
            'branch_id' => ['sometimes', 'nullable', 'integer', 'exists:branches,id'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 15);
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    public function nameQuery(): ?string
    {
        return $this->input('nameQuery');
    }

    public function sourceFilter(): ?string
    {
        return $this->input('source');
    }

    public function utmCampaignFilter(): ?string
    {
        return $this->input('utm_campaign');
    }

    public function utmMediumFilter(): ?string
    {
        return $this->input('utm_medium');
    }

    public function period(): string
    {
        return $this->input('period', 'currentmonth');
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

    public function orderDir(): string
    {
        return $this->input('order-dir', 'desc');
    }

    public function orderType(): ?string
    {
        return $this->input('order_type');
    }

    public function branchId(): ?int
    {
        $value = $this->input('branch_id');
        return $value !== null ? (int) $value : null;
    }

    public function export(): bool
    {
        return (bool) $this->input('export', false);
    }
}

