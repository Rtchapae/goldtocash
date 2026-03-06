<?php

namespace App\Domain\Reviews\Providers;

use App\Domain\Reviews\Repositories\TrustpilotRepository;
use App\Domain\Reviews\Services\TrustpilotService;
use Illuminate\Support\ServiceProvider;

class TrustpilotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TrustpilotRepository::class, function ($app) {
            return new TrustpilotRepository();
        });

        $this->app->singleton(TrustpilotService::class, function ($app) {
            return new TrustpilotService(
                $app->make(TrustpilotRepository::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
