<?php

namespace App\Domain\Orders\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CalculatorController extends Controller
{
    public function getCurrentPrice(): JsonResponse
    {
        $pricePerTOz = Cache::get('gold-price');

        return response()->json([
            'data' => [
                'price' => $pricePerTOz ? (float) $pricePerTOz : 0,
            ],
        ]);
    }
}

