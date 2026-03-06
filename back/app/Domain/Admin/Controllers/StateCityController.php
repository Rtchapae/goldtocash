<?php

namespace App\Domain\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StateCityController extends Controller
{
    /**
     * Get states and cities picklist
     */
    public function index(): JsonResponse
    {
        $data = $this->getStateAndCityPicklist();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function getCities(string $state): JsonResponse
    {
        $picklist = $this->getStateAndCityPicklist();

        if (strlen($state) === 2) {
            $abbreviations = config('state_abbreviations', []);
            $stateFullName = $abbreviations[strtoupper($state)] ?? null;
            if ($stateFullName) {
                $state = $stateFullName;
            }
        }

        $cities = $picklist[$state] ?? [];

        return response()->json([
            'status' => true,
            'data' => $cities,
        ]);
    }

    private function getStateAndCityPicklist(): array
    {
        return config('states_cities', []);
    }
}
