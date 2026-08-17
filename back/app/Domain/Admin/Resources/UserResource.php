<?php

namespace App\Domain\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stateRaw = $this->state;
        $state = $stateRaw;
        if ($state && strlen($state) === 2) {
            $abbreviations = config('state_abbreviations', []);
            $state = $abbreviations[strtoupper($state)] ?? $state;
        }

        $governmentIdParams = $this->government_id_params;
        if (is_string($governmentIdParams) && $governmentIdParams !== '') {
            $decoded = json_decode($governmentIdParams, true);
            $governmentIdParams = is_array($decoded) ? $decoded : [];
        } elseif (! is_array($governmentIdParams)) {
            $governmentIdParams = [];
        }

        return [
            'id' => $this->id,
            'source_badges' => $this->source_badges,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'address2' => $this->address2 ?? null,
            'city' => $this->city,
            'state' => $state,
            'state_code' => $stateRaw && strlen((string) $stateRaw) === 2
                ? strtoupper((string) $stateRaw)
                : ($stateRaw ?? null),
            'zip' => $this->zip,
            'country' => $this->country ?? null,
            'orders_summary' => $this->orders_summary,
            'payment_method' => $this->payment_method ?? null,
            'government_id' => $this->government_id ?? null,
            'government_id_params' => $governmentIdParams,
            'government_id_number' => $governmentIdParams['idNumber'] ?? $governmentIdParams['id_number'] ?? null,
            'state_issued' => $governmentIdParams['issuer'] ?? null,
            'date_of_birth' => $this->date_of_birth ? (\Carbon\Carbon::parse($this->date_of_birth)->format('Y-m-d')) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


