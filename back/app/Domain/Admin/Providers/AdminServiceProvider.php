<?php

namespace App\Domain\Admin\Providers;

use App\Domain\Admin\Actions\GetDashboardAnalyticsAction;
use App\Domain\Admin\Actions\GetRecentActivitiesAction;
use App\Domain\Admin\Repositories\ModelHistoryRepository;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModelHistoryRepository::class, function ($app) {
            return new ModelHistoryRepository();
        });

        $this->app->singleton(GetRecentActivitiesAction::class, function ($app) {
            return new GetRecentActivitiesAction(
                $app->make(ModelHistoryRepository::class)
            );
        });

        $this->app->extend(GetDashboardAnalyticsAction::class, function ($service, $app) {
            return new GetDashboardAnalyticsAction(
                $app->make(\App\Domain\Orders\Repositories\OrderRepositoryInterface::class),
                $app->make(\App\Domain\Users\Repositories\BranchRepositoryInterface::class),
                $app->make(\App\Domain\Users\Repositories\SessionRepositoryInterface::class),
                $app->make(GetRecentActivitiesAction::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
