<?php

namespace App\Domain\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstName = (string) ($this->first_name ?? '');
        $lastName = (string) ($this->last_name ?? '');

        if (trim($firstName) === '' && trim($lastName) === '') {
            $fullName = trim((string) $this->name);
            $parts = $fullName !== '' ? preg_split('/\s+/', $fullName) : [];
            $firstName = (string) ($parts[0] ?? '');
            $lastName = $parts ? implode(' ', array_slice($parts, 1)) : '';
        }

        $name = (string) ($this->name ?? '');
        if (trim($name) === '' && (trim($firstName) !== '' || trim($lastName) !== '')) {
            $name = trim(trim($firstName) . ' ' . trim($lastName));
        }

        $paymentMethodParams = $this->payment_method_params;
        if (is_string($paymentMethodParams) && $paymentMethodParams !== '') {
            $decoded = json_decode($paymentMethodParams, true);
            $paymentMethodParams = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($paymentMethodParams)) {
            $paymentMethodParams = [];
        }

        $governmentIdParams = $this->government_id_params;
        if (is_string($governmentIdParams) && $governmentIdParams !== '') {
            $decoded = json_decode($governmentIdParams, true);
            $governmentIdParams = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($governmentIdParams)) {
            $governmentIdParams = [];
        }

        return [
            'id' => $this->id,
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'payment_method' => $this->payment_method,
            'payment_method_params' => $paymentMethodParams,
            'government_id' => $this->government_id,
            'government_id_params' => $governmentIdParams,
            'date_of_birth' => $this->date_of_birth,
            'verify' => $this->verify,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

