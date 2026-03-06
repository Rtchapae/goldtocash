<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Users\Controllers\FrontAuthController;
use App\Domain\Orders\Controllers\OrderController;
use App\Domain\Users\Controllers\FrontUserProfileController;
use App\Domain\Users\Controllers\FrontUserDocumentController;
use App\Domain\Users\Controllers\PhoneVerificationController;
use App\Domain\Messages\Controllers\FrontMessageController;
use App\Domain\Posts\Controllers\FrontPostController;
use App\Domain\Admin\Controllers\StateCityController;
use App\Domain\Admin\Controllers\CountryController;
use App\Domain\Seo\Controllers\FrontSeoController;
use App\Domain\Seo\Controllers\FrontSitemapController;
use App\Domain\Reviews\Controllers\TrustpilotController;

Route::get('calculator/current-price', [\App\Domain\Orders\Controllers\CalculatorController::class, 'getCurrentPrice']);
Route::post('kit/register', [OrderController::class, 'registerKit']);
Route::get('recent-payout', [\App\Domain\Orders\Controllers\RecentPayoutController::class, 'getRandomRecentPayout']);

Route::prefix('user')->group(function (): void {
    Route::post('send-code', [PhoneVerificationController::class, 'sendCode']);
    Route::post('verify-code', [PhoneVerificationController::class, 'verifyCode']);
});

Route::prefix('states-cities')->group(function (): void {
    Route::get('/', [StateCityController::class, 'index']);
    Route::get('{state}/cities', [StateCityController::class, 'getCities']);
});

Route::get('countries', [CountryController::class, 'index']);

Route::get('seo', [FrontSeoController::class, 'getSeoData']);
Route::get('sitemap-urls', [FrontSitemapController::class, 'getUrls']);

Route::get('trustpilot/reviews', [TrustpilotController::class, 'getReviews']);

Route::prefix('posts')->group(function (): void {
    Route::get('/', [FrontPostController::class, 'index']);
    Route::get('{slug}', [FrontPostController::class, 'show']);
});

Route::prefix('front/auth')->group(function (): void {
    Route::post('login', [FrontAuthController::class, 'login']);
    Route::post('forgot-password', [FrontAuthController::class, 'forgotPassword']);
    Route::middleware('auth:front')->group(function (): void {
        Route::get('me', [FrontAuthController::class, 'me']);
        Route::post('refresh', [FrontAuthController::class, 'refresh']);
        Route::post('logout', [FrontAuthController::class, 'logout']);
    });
});

Route::middleware('auth:front')->group(function (): void {
    Route::get('orders/{orderId}/print-label', [OrderController::class, 'getPrintLabel']);
    Route::post('orders/{orderId}/confirm-account', [OrderController::class, 'confirmAccountCreation']);

    Route::prefix('front')->group(function (): void {
        Route::prefix('user')->group(function (): void {
            Route::prefix('orders')->group(function (): void {
                Route::get('/', [OrderController::class, 'getUserOrders']);
                Route::post('create', [OrderController::class, 'createKitRequest']);
                Route::post('{orderId}/offer-response', [OrderController::class, 'respondToOffer']);
                Route::get('{orderId}/letter', [OrderController::class, 'viewLetter']);
            });

            Route::put('profile', [FrontUserProfileController::class, 'update']);

            Route::prefix('documents')->group(function (): void {
                Route::get('/', [FrontUserDocumentController::class, 'index']);
                Route::post('upload', [FrontUserDocumentController::class, 'upload']);
                Route::get('{id}/view', [FrontUserDocumentController::class, 'view']);
            });

            Route::prefix('messages')->group(function (): void {
                Route::get('/', [FrontMessageController::class, 'index']);
                Route::post('/', [FrontMessageController::class, 'send']);
                Route::post('mark-read', [FrontMessageController::class, 'markAsRead']);
            });
        });
    });
});
