<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Users\Repositories\EloquentUserRepository;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Repositories\EloquentOrderRepository;
use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Messages\Repositories\EloquentMessageRepository;
use App\Domain\Sms\Repositories\SmsLookupRepositoryInterface;
use App\Domain\Sms\Repositories\EloquentSmsLookupRepository;
use App\Domain\Users\Repositories\TraceRepositoryInterface;
use App\Domain\Users\Repositories\EloquentTraceRepository;
use App\Domain\Users\Repositories\RoleRepositoryInterface;
use App\Domain\Users\Repositories\EloquentRoleRepository;
use App\Domain\Users\Repositories\BranchRepositoryInterface;
use App\Domain\Users\Repositories\EloquentBranchRepository;
use App\Domain\Sms\Repositories\SentMessageRepositoryInterface;
use App\Domain\Sms\Repositories\EloquentSentMessageRepository;
use App\Domain\Sms\Repositories\SentMessageReceiptRepositoryInterface;
use App\Domain\Sms\Repositories\EloquentSentMessageReceiptRepository;
use App\Domain\Sms\Repositories\SmsInboundRepositoryInterface;
use App\Domain\Sms\Repositories\EloquentSmsInboundRepository;
use App\Domain\Sms\Repositories\SmsOptinRepositoryInterface;
use App\Domain\Sms\Repositories\EloquentSmsOptinRepository;
use App\Domain\Sms\Repositories\SmsOptoutRepositoryInterface;
use App\Domain\Sms\Repositories\EloquentSmsOptoutRepository;
use App\Domain\Posts\Repositories\AdminPostRepositoryInterface;
use App\Domain\Posts\Repositories\FrontPostRepositoryInterface;
use App\Domain\Posts\Repositories\EloquentPostRepository;
use App\Domain\Admin\Repositories\ExpenseRepositoryInterface;
use App\Domain\Admin\Repositories\EloquentExpenseRepository;
use App\Domain\Users\Repositories\TraceEventRepositoryInterface;
use App\Domain\Users\Repositories\EloquentTraceEventRepository;
use App\Domain\Users\Repositories\SessionRepositoryInterface;
use App\Domain\Users\Repositories\EloquentSessionRepository;
use App\Domain\Seo\Repositories\SeoPageRepositoryInterface;
use App\Domain\Seo\Repositories\EloquentSeoPageRepository;
use App\Domain\Admin\Actions\ListNotificationsAction;
use App\Domain\Admin\Actions\MarkNotificationsAsReadAction;
use App\Domain\Admin\Actions\GetNotificationCountAction;
use App\Domain\Admin\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, EloquentMessageRepository::class);
        $this->app->bind(SmsLookupRepositoryInterface::class, EloquentSmsLookupRepository::class);
        $this->app->bind(TraceRepositoryInterface::class, EloquentTraceRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, EloquentBranchRepository::class);
        $this->app->bind(SentMessageRepositoryInterface::class, EloquentSentMessageRepository::class);
        $this->app->bind(SentMessageReceiptRepositoryInterface::class, EloquentSentMessageReceiptRepository::class);
        $this->app->bind(SmsInboundRepositoryInterface::class, EloquentSmsInboundRepository::class);
        $this->app->bind(SmsOptinRepositoryInterface::class, EloquentSmsOptinRepository::class);
        $this->app->bind(SmsOptoutRepositoryInterface::class, EloquentSmsOptoutRepository::class);
        $this->app->bind(AdminPostRepositoryInterface::class, EloquentPostRepository::class);
        $this->app->bind(FrontPostRepositoryInterface::class, EloquentPostRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, EloquentExpenseRepository::class);
        $this->app->bind(TraceEventRepositoryInterface::class, EloquentTraceEventRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, EloquentSessionRepository::class);
        $this->app->bind(SeoPageRepositoryInterface::class, EloquentSeoPageRepository::class);

        // Notification services
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(ListNotificationsAction::class);
        $this->app->singleton(MarkNotificationsAsReadAction::class);
        $this->app->singleton(GetNotificationCountAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Morph map: legacy model_type in model_histories → domain models
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'App\Models\Order' => \App\Domain\Orders\Models\Order::class,
            'App\Models\User' => \App\Domain\Users\Models\User::class,
        ]);
    }
}
