<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\DTOs\TraceSourceDto;
use App\Domain\Users\Models\TraceEvent;
use App\Domain\Users\Repositories\TraceEventRepositoryInterface;
use App\Domain\Users\Repositories\TraceRepositoryInterface;
use Carbon\Carbon;

class QueryTraceConversionsAction
{
    public function __construct(
        private readonly TraceRepositoryInterface $traceRepository,
        private readonly TraceEventRepositoryInterface $traceEventRepository,
    ) {
    }

    public function execute(
        ?string $source = null,
        ?string $campaign = null,
        ?string $medium = null,
        ?string $term = null,
        ?string $content = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ): array {
        $dateRange = $this->determineDateRange($dateFrom, $dateTo);
        $dateFromCarbon = $dateRange['from'];
        $dateToCarbon = $dateRange['to'];

        $traceSource = TraceSourceDto::createFromArray([
            'source' => $source,
            'campaign' => $campaign,
            'medium' => $medium,
            'term' => $term,
            'content' => $content,
        ]);

        $traceCount = $this->traceRepository->countBetween($dateFromCarbon, $dateToCarbon, $traceSource);
        $kitReqCount = $this->traceEventRepository->countByTypeBetween(
            TraceEvent::EVENT_KIT_REQUEST,
            $dateFromCarbon,
            $dateToCarbon,
            $traceSource
        );
        $itemsReceivedCount = $this->traceEventRepository->countByTypeBetween(
            TraceEvent::EVENT_ITEMS_RECEIVED,
            $dateFromCarbon,
            $dateToCarbon,
            $traceSource
        );
        $offerAcceptedCount = $this->traceEventRepository->countByTypeBetween(
            TraceEvent::EVENT_OFFER_ACCEPTED,
            $dateFromCarbon,
            $dateToCarbon,
            $traceSource
        );

        $conversions = [
            'traceCount' => $traceCount,
            'kitReqCount' => $kitReqCount,
            'itemsReceivedCount' => $itemsReceivedCount,
            'offerAcceptedCount' => $offerAcceptedCount,
            'traceToKit' => $this->calculateConversion($kitReqCount, $traceCount),
            'traceToReceived' => $this->calculateConversion($itemsReceivedCount, $traceCount),
            'kitToReceived' => $this->calculateConversion($itemsReceivedCount, $kitReqCount),
            'traceToAccepted' => $this->calculateConversion($offerAcceptedCount, $traceCount),
            'kitToAccepted' => $this->calculateConversion($offerAcceptedCount, $kitReqCount),
            'receivedToAccepted' => $this->calculateConversion($offerAcceptedCount, $itemsReceivedCount),
        ];

        $appliedFilters = [];
        if ($source) {
            $appliedFilters['source'] = $source;
        }
        if ($campaign) {
            $appliedFilters['campaign'] = $campaign;
        }
        if ($medium) {
            $appliedFilters['medium'] = $medium;
        }
        if ($term) {
            $appliedFilters['term'] = $term;
        }
        if ($content) {
            $appliedFilters['content'] = $content;
        }
        $appliedFilters['dateFrom'] = $dateFromCarbon->toDateString();
        $appliedFilters['dateTo'] = $dateToCarbon->toDateString();

        return [
            'conversions' => $conversions,
            'appliedFilters' => $appliedFilters,
        ];
    }

    private function determineDateRange(?string $dateFrom, ?string $dateTo): array
    {
        $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : null;

        if ($from && $to) {
            return ['from' => $from, 'to' => $to];
        } elseif ($from) {
            return ['from' => $from, 'to' => $from->clone()->addDays(7)->endOfDay()];
        } elseif ($to) {
            return ['from' => $to->clone()->subDays(7)->startOfDay(), 'to' => $to];
        }

        return [
            'from' => Carbon::now()->subDays(7)->startOfDay(),
            'to' => Carbon::now()->endOfDay(),
        ];
    }

    private function calculateConversion(int $thisIs, int $of): float
    {
        if ($of === 0) {
            return 0;
        }
        return round(($thisIs / $of) * 100, 2);
    }
}

