<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Actions\GetRecentActivitiesAction;
use App\Domain\Admin\Enums\PeriodFilter;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderType;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Users\Repositories\BranchRepositoryInterface;
use App\Domain\Users\Repositories\SessionRepositoryInterface;
use App\Support\AdminPeriodQuery;

class GetDashboardAnalyticsAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly BranchRepositoryInterface $branchRepository,
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly GetRecentActivitiesAction $getRecentActivitiesAction,
    ) {
    }

    public function execute(?string $period = null, ?string $from = null, ?string $to = null): array
    {
        $periodEnum = $period ? PeriodFilter::tryFrom($period) : null;
        if (!$periodEnum) {
            $periodEnum = PeriodFilter::default();
        }

        $branches = $this->branchRepository->getAllOrderedByDisplayName();

        $online = $this->getOnlineAnalytics($periodEnum, $from, $to);

        $offline = [];
        foreach ($branches as $branch) {
            $branchKey = strtolower($branch->display_name);
            $offline[$branchKey] = $this->getOfflineAnalyticsForBranch($branch->id, $periodEnum, $from, $to);
        }

        $offlineCombined = $this->getOfflineAnalyticsCombined($periodEnum, $from, $to);

        $allCombined = $this->getAllCombinedAnalytics($periodEnum, $from, $to);

        $charts = $this->getChartData($branches);

        $recentActivities = $this->getRecentActivitiesAction->execute();

        return [
            'online' => $online,
            'offline' => $offline,
            'offlineCombined' => $offlineCombined,
            'allCombined' => $allCombined,
            'charts' => $charts,
            'recentActivities' => $recentActivities,
        ];
    }

    private function getChartData($branches): array
    {
        $onlineVisitors = $this->sessionRepository->getVisitorsByMonth();
        $onlineOrders = $this->orderRepository->getOrdersByMonth(OrderType::ONLINE->value);
        $onlinePaidAmount = $this->orderRepository->getPaidAmountByMonth(OrderType::ONLINE->value);

        $offlineCharts = [];
        foreach ($branches as $branch) {
            $branchKey = strtolower($branch->display_name);
            $offlineCharts[$branchKey] = [
                'transactions' => $this->orderRepository->getOrdersByMonth(OrderType::OFFLINE->value, $branch->id),
                'paidAmount' => $this->orderRepository->getPaidAmountByMonth(OrderType::OFFLINE->value, $branch->id),
            ];
        }

        $offlineCombinedCharts = [
            'transactions' => $this->orderRepository->getOrdersByMonth(OrderType::OFFLINE->value),
            'paidAmount' => $this->orderRepository->getPaidAmountByMonth(OrderType::OFFLINE->value),
        ];

        return [
            'online' => [
                'visitors' => $onlineVisitors,
                'orders' => $onlineOrders,
                'paidAmount' => $onlinePaidAmount,
            ],
            'offline' => $offlineCharts,
            'offlineCombined' => $offlineCombinedCharts,
        ];
    }

    private function getOnlineAnalytics(PeriodFilter $period, ?string $from, ?string $to): array
    {
        $query = $this->orderRepository->getQueryBuilder()
            ->where('orders.order_type', OrderType::ONLINE->value);

        $this->applyTimeFilter($query, $period, $from, $to);

        $ordersCount = (clone $query)->count();
        $transactionsCount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->count();
        $paidAmount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->sum('orders.amount') ?? 0;

        $sessionQuery = $this->sessionRepository->getQueryBuilder();
        $this->applySessionTimeFilter($sessionQuery, $period, $from, $to);
        $visitors = $this->sessionRepository->sumVisitors($sessionQuery);

        return [
            'visitors' => $visitors,
            'orders' => $ordersCount,
            'transactions' => $transactionsCount,
            'paidAmount' => (float) $paidAmount,
        ];
    }

    private function getOfflineAnalyticsForBranch(int $branchId, PeriodFilter $period, ?string $from, ?string $to): array
    {
        $query = $this->orderRepository->getQueryBuilder()
            ->where('orders.order_type', OrderType::OFFLINE->value)
            ->where('orders.branch_id', $branchId);

        $this->applyTimeFilter($query, $period, $from, $to);

        $transactionsCount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->count();
        $paidAmount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->sum('orders.amount') ?? 0;

        return [
            'transactions' => $transactionsCount,
            'paidAmount' => (float) $paidAmount,
        ];
    }

    private function getOfflineAnalyticsCombined(PeriodFilter $period, ?string $from, ?string $to): array
    {
        $query = $this->orderRepository->getQueryBuilder()
            ->where('orders.order_type', OrderType::OFFLINE->value);

        $this->applyTimeFilter($query, $period, $from, $to);

        $transactionsCount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->count();
        $paidAmount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->sum('orders.amount') ?? 0;

        return [
            'transactions' => $transactionsCount,
            'paidAmount' => (float) $paidAmount,
        ];
    }

    private function getAllCombinedAnalytics(PeriodFilter $period, ?string $from, ?string $to): array
    {
        $query = $this->orderRepository->getQueryBuilder();

        $this->applyTimeFilter($query, $period, $from, $to);

        $transactionsCount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->count();
        $paidAmount = (clone $query)->where('orders.status', OrderStatus::PAID->value)->sum('orders.amount') ?? 0;

        return [
            'transactions' => $transactionsCount,
            'paidAmount' => (float) $paidAmount,
        ];
    }

    private function applyTimeFilter($query, PeriodFilter $period, ?string $from, ?string $to): void
    {
        AdminPeriodQuery::apply($query, $period->value, $from, $to);
    }

    private function applySessionTimeFilter($query, PeriodFilter $period, ?string $from, ?string $to): void
    {
        AdminPeriodQuery::apply($query, $period->value, $from, $to, 'sessions.created_at');
    }

}

