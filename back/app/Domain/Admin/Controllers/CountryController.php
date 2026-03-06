<?php

namespace App\Domain\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $countries = config('countries', []);

        return response()->json([
            'status' => true,
            'data' => $countries,
        ]);
    }
}




