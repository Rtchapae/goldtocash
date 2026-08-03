<?php

namespace App\Domain\Orders\Actions;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Users\Repositories\BranchRepositoryInterface;
use App\Domain\Users\Repositories\TraceRepositoryInterface;
use App\Support\AdminPeriodQuery;
use App\Support\AdminUserSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ListPaidOrdersAction
{
    private array $availableSources = [];
    private array $availableUtmCampaigns = [];
    private array $availableUtmMediums = [];
    private array $availableBranches = [];

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly TraceRepositoryInterface $traceRepository,
        private readonly BranchRepositoryInterface $branchRepository,
    ) {
    }

    public function execute(
        int $perPage = 15,
        int $page = 1,
        ?string $nameQuery = null,
        ?string $sourceFilter = null,
        ?string $utmCampaignFilter = null,
        ?string $utmMediumFilter = null,
        string $period = 'currentmonth',
        ?string $from = null,
        ?string $to = null,
        ?string $orderBy = null,
        string $orderDir = 'desc',
    ): LengthAwarePaginator {
        $this->loadFilterOptions();

        $query = $this->orderRepository->getQueryBuilder()
            ->where('orders.status', OrderStatus::PAID->value);

        $this->applyTimeFilter($query, $period, $from, $to);

        $this->applyTraceFilters($query, $sourceFilter, $utmCampaignFilter, $utmMediumFilter);

        AdminUserSearch::apply($query, $nameQuery);

        $this->applySorting($query, $orderBy, $orderDir);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $paginator->getCollection()->transform(function (Order $order) {
            $userHasOtherOrder = $this->orderRepository->userHasOtherOrders(
                $order->user_id,
                $order->id
            );

            $order->setAttribute('is_returning_customer', $userHasOtherOrder);

            return $order;
        });

        return $paginator;
    }

    private function applyTimeFilter(
        Builder $query,
        string $period,
        ?string $from,
        ?string $to,
    ): void {
        AdminPeriodQuery::apply($query, $period, $from, $to);
    }

    private function applyTraceFilters(
        Builder $query,
        ?string $sourceFilter,
        ?string $utmCampaignFilter,
        ?string $utmMediumFilter
    ): void {
        if ((!$sourceFilter || $sourceFilter === 'any') &&
            (!$utmCampaignFilter || $utmCampaignFilter === 'any') &&
            (!$utmMediumFilter || $utmMediumFilter === 'any')) {
            return;
        }

        $query->join('traces', function ($join) use ($sourceFilter, $utmCampaignFilter, $utmMediumFilter) {
            $join->on('traces.user_id', '=', 'users.id');

            if ($sourceFilter && $sourceFilter !== 'any') {
                $join->whereRaw("JSON_EXTRACT(traces.source, '$.utm_source') = ?", [$sourceFilter]);
            }

            if ($utmCampaignFilter && $utmCampaignFilter !== 'any') {
                $join->whereRaw("JSON_EXTRACT(traces.source, '$.utm_campaign') = ?", [$utmCampaignFilter]);
            }

            if ($utmMediumFilter && $utmMediumFilter !== 'any') {
                $join->whereRaw("JSON_EXTRACT(traces.source, '$.utm_medium') = ?", [$utmMediumFilter]);
            }
        });
    }

    private function applySorting(Builder $query, ?string $orderBy, string $orderDir): void
    {
        if (!$orderBy) {
            $query->orderBy('orders.id', 'desc');
            return;
        }

        $direction = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        $columnMap = [
            'order_name' => 'users.name',
            'order_code' => 'orders.id',
            'order_create' => 'orders.created_at',
            'order_price' => 'orders.amount',
            'order_status' => 'orders.status',
        ];

        if (isset($columnMap[$orderBy])) {
            $query->orderBy($columnMap[$orderBy], $direction);
        } elseif (in_array($orderBy, ['name', 'email', 'phone'])) {
            $query->orderBy("users.{$orderBy}", $direction);
        } else {
            $query->orderBy("orders.{$orderBy}", $direction);
        }
    }

    private function loadFilterOptions(): void
    {
        $filterOptions = $this->traceRepository->getFilterOptions(OrderStatus::PAID->value);

        $this->availableSources = $filterOptions['sources'];
        $this->availableUtmCampaigns = $filterOptions['utm_campaigns'];
        $this->availableUtmMediums = $filterOptions['utm_mediums'];

        $this->availableBranches = $this->branchRepository
            ->getAllOrderedByDisplayName()
            ->map(fn ($branch) => [
                'id' => $branch->id,
                'label' => $branch->display_name ?: $branch->name,
            ])
            ->values()
            ->all();

        sort($this->availableSources);
        sort($this->availableUtmCampaigns);
        sort($this->availableUtmMediums);
    }

    public function getAvailableSources(): array
    {
        return $this->availableSources;
    }

    public function getAvailableUtmCampaigns(): array
    {
        return $this->availableUtmCampaigns;
    }

    public function getAvailableUtmMediums(): array
    {
        return $this->availableUtmMediums;
    }

    public function getAvailableBranches(): array
    {
        return $this->availableBranches;
    }
}

