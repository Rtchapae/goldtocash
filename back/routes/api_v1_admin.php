<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Admin\Controllers\AdminAuthController;
use App\Domain\Admin\Controllers\UserController;
use App\Domain\Admin\Controllers\OrderController;
use App\Domain\Admin\Controllers\AdminUserController;
use App\Domain\Admin\Controllers\ExpenseController;
use App\Domain\Admin\Controllers\TraceEventController;
use App\Domain\Admin\Controllers\BranchController;
use App\Domain\Admin\Controllers\MessageController;
use App\Domain\Admin\Controllers\CountersController;
use App\Domain\Admin\Controllers\SmsController;
use App\Domain\Admin\Controllers\PostController;
use App\Domain\Admin\Controllers\DashboardController;
use App\Domain\Users\Controllers\UserNoteController;

Route::middleware(['api'])->group(function () {

Route::prefix('admin/auth')->group(function (): void {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::middleware(['auth:admin', 'admin'])->group(function (): void {
        Route::get('me', [AdminAuthController::class, 'me']);
        Route::post('refresh', [AdminAuthController::class, 'refresh']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
    });
    });
});

Route::get('admin/orders/{id}/pdf-html', [OrderController::class, 'previewPdfHtml']);
Route::prefix('admin')
    ->middleware(['auth:admin', 'admin'])
    ->group(function (): void {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::post('users', [UserController::class, 'store']);
        Route::prefix('users/note')->group(function (): void {
            Route::get('query', [UserNoteController::class, 'query']);
            Route::post('update-or-create', [UserNoteController::class, 'updateOrCreate']);
            Route::delete('delete', [UserNoteController::class, 'delete']);
        });

        Route::prefix('orders')->group(function (): void {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('pending', [OrderController::class, 'pending']);
            Route::get('paid', [OrderController::class, 'paid']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::put('{id}', [OrderController::class, 'update']);
            Route::get('{id}/files', [OrderController::class, 'getFiles']);
            Route::get('{id}/files/{filename}', [OrderController::class, 'downloadFile']);
            Route::get('{id}/pdf', [OrderController::class, 'generatePdf']);
            Route::put('{id}/shipping', [OrderController::class, 'updateShipping']);
        });

        Route::prefix('seo-pages')->group(function (): void {
            Route::get('/', [\App\Domain\Seo\Controllers\SeoPageController::class, 'index']);
            Route::post('/', [\App\Domain\Seo\Controllers\SeoPageController::class, 'store']);
            Route::get('{id}', [\App\Domain\Seo\Controllers\SeoPageController::class, 'show']);
            Route::put('{id}', [\App\Domain\Seo\Controllers\SeoPageController::class, 'update']);
            Route::delete('{id}', [\App\Domain\Seo\Controllers\SeoPageController::class, 'destroy']);
        });

        Route::prefix('admin-users')->group(function (): void {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::post('/', [AdminUserController::class, 'store']);
            Route::put('{id}', [AdminUserController::class, 'update']);
            Route::delete('{id}', [AdminUserController::class, 'destroy']);
        });

        Route::prefix('expenses')->group(function (): void {
            Route::get('/', [ExpenseController::class, 'index']);
            Route::get('export', [ExpenseController::class, 'exportCsv']);
        });

        Route::prefix('trace')->group(function (): void {
            Route::get('events/query', [TraceEventController::class, 'query']);
            Route::get('conversions/query', [TraceEventController::class, 'queryConversions']);
            Route::post('campaign-stats/query', [TraceEventController::class, 'queryCampaignStats']);
            Route::get('filter-options', [TraceEventController::class, 'getFilterOptions']);
        });

        Route::get('branches', [BranchController::class, 'index']);

        Route::prefix('states-cities')->group(function (): void {
            Route::get('/', [\App\Domain\Admin\Controllers\StateCityController::class, 'index']);
            Route::get('{state}/cities', [\App\Domain\Admin\Controllers\StateCityController::class, 'getCities']);
        });

        Route::get('countries', [\App\Domain\Admin\Controllers\CountryController::class, 'index']);

        Route::prefix('messages')->group(function (): void {
            Route::get('/', [MessageController::class, 'index']);
            Route::get('{threadId}', [MessageController::class, 'show']);
            Route::post('{threadId}/send', [MessageController::class, 'send']);
        });

        Route::get('counters', [CountersController::class, 'index']);
        Route::get('counters/analytics', [CountersController::class, 'analytics']);

        Route::prefix('notifications')->group(function (): void {
            Route::get('/', [\App\Domain\Admin\Controllers\NotificationController::class, 'index']);
            Route::post('mark-read', [\App\Domain\Admin\Controllers\NotificationController::class, 'markAllAsRead']);
            Route::get('count', [\App\Domain\Admin\Controllers\NotificationController::class, 'getCount']);
            Route::post('{id}/mark-read', [\App\Domain\Admin\Controllers\NotificationController::class, 'markAsRead']);
        });

        Route::prefix('sms')->group(function (): void {
            Route::post('search', [SmsController::class, 'search']);
        });

        Route::prefix('posts')->group(function (): void {
            Route::get('/', [PostController::class, 'index']);
            Route::post('/', [PostController::class, 'store']);
            Route::post('upload-image', [PostController::class, 'uploadImage']);
            Route::get('{id}', [PostController::class, 'show'])->whereNumber('id');
            Route::put('{id}', [PostController::class, 'update'])->whereNumber('id');
            // Multipart: PHP often omits files on PUT — POST with numeric id only (never matches "upload-image").
            Route::post('{id}', [PostController::class, 'update'])->whereNumber('id');
            Route::delete('{id}', [PostController::class, 'destroy'])->whereNumber('id');
        });

        Route::prefix('builder-pages')->group(function (): void {
            Route::get('/', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'index']);
            Route::post('/', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'store']);
            Route::post('upload-image', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'uploadImage']);
            Route::post('import-docx', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'importDocx']);
            Route::get('{id}', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'show'])->whereNumber('id');
            Route::put('{id}', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'update'])->whereNumber('id');
            Route::delete('{id}', [\App\Domain\Admin\Controllers\BuilderPageController::class, 'destroy'])->whereNumber('id');
        });

    });





