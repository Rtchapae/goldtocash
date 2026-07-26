<?php

namespace App\Domain\Orders\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CalculatorController extends Controller
{
    public function getCurrentPrice(): JsonResponse
    {
        $pricePerTOz = Cache::get('gold-price');

        // Local / cold cache: populate via the same job the scheduler runs every 3h
        if (! $pricePerTOz) {
            Artisan::call('update-gold-price');
            $pricePerTOz = Cache::get('gold-price');
        }

        return response()->json([
            'data' => [
                'price' => $pricePerTOz ? (float) $pricePerTOz : 0,
            ],
        ]);
    }
}

