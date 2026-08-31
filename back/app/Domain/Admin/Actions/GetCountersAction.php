<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Enums\PeriodFilter;
use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Support\AdminPeriodQuery;
use Illuminate\Support\Facades\Auth;

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
        AdminPeriodQuery::apply($query, $period->value, $from, $to);
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

