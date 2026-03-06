<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Enums\PeriodFilter;
use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GetCountersAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly MessageRepositoryInterface $messageRepository,
    ) {
    }

    public function execute(?string $period = null, ?string $from = null, ?string $to = null): array
    {
        $periodEnum = $period ? PeriodFilter::tryFrom($period) : null;
        if (!$periodEnum) {
            $periodEnum = PeriodFilter::default();
        }

        $appraisalRequests = $this->getOrdersCountByStatus(
            [
                OrderStatus::KIT_REQUESTED->value,
                OrderStatus::IN_TRANSIT->value,
                OrderStatus::ITEMS_RECEIVED->value,
                OrderStatus::APPRAISAL->value,
            ],
            $periodEnum,
            $from,
            $to
        );

        $offersMade = $this->getOrdersCountByStatus(
            [OrderStatus::OFFER_SENT->value],
            $periodEnum,
            $from,
            $to
        );

        return [
            'appraisalRequests' => $appraisalRequests,
            'offersMade' => $offersMade,
        ];
    }

    private function getOrdersCountByStatus(array $statuses, PeriodFilter $period, ?string $from, ?string $to): int
    {
        $query = $this->orderRepository->getQueryBuilder()
            ->whereIn('orders.status', $statuses);

        $this->applyTimeFilter($query, $period, $from, $to);

        return $query->count();
    }

    private function applyTimeFilter($query, PeriodFilter $period, ?string $from, ?string $to): void
    {
        if ($period === PeriodFilter::ALL) {
            return;
        }

        $now = Carbon::now();

        match ($period) {
            PeriodFilter::TODAY => $query->where('orders.created_at', '>=', $now->toDateString()),
            PeriodFilter::YESTERDAY => $query->where('orders.created_at', '>=', $now->copy()->subDay()->toDateString())
                ->where('orders.created_at', '<', $now->toDateString()),
            PeriodFilter::LAST_MONTH => $query->where('orders.created_at', '>=', $now->copy()->subMonth()->startOfMonth()->toDateString())
                ->where('orders.created_at', '<', $now->startOfMonth()->toDateString()),
            PeriodFilter::CUSTOM => $this->applyCustomPeriodFilter($query, $from, $to),
            PeriodFilter::CURRENT_MONTH => $query->where('orders.created_at', '>=', $now->startOfMonth()->toDateString()),
        };
    }

    private function applyCustomPeriodFilter($query, ?string $from, ?string $to): void
    {
        if ($from) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $query->where('orders.created_at', '>=', $fromDate);
        }
        if ($to) {
            $toDate = Carbon::parse($to)->endOfDay();
            $query->where('orders.created_at', '<=', $toDate);
        }
    }

    private function getUnreadMessagesCount(): int
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return 0;
        }

        return $this->messageRepository->getUnreadMessagesCountForAdmin($admin->id);
    }
}

