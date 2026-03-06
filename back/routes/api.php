<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    require __DIR__ . '/api_v1_front.php';
    require __DIR__ . '/api_v1_admin.php';
});

Route::prefix('sms')->group(function (): void {
    Route::post('ingest-receipt', [\App\Domain\Sms\Controllers\ExternalSmsController::class, 'ingestReceipt']);
    Route::post('ingest-inbound', [\App\Domain\Sms\Controllers\ExternalSmsController::class, 'ingestInbound']);
});

Route::get('calculator/current-price', [\App\Domain\Orders\Controllers\CalculatorController::class, 'getCurrentPrice']);

