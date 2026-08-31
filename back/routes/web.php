<?php

use App\Domain\Contact\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/** Legacy contact form endpoint (Start Bootstrap–style action="/sendmail") */
Route::post('/sendmail', [ContactController::class, 'send']);
