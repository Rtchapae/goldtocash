<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\Repositories\TraceRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class GetTraceFilterOptionsAction
{
    public function __construct(
        private readonly TraceRepositoryInterface $traceRepository,
    ) {
    }

    public function execute(): array
    {
        return Cache::remember('trace_filter_options', 3600, function () {
            $options = $this->traceRepository->getFilterOptions();

            $sources = $options['sources'] ?? [];
            $utmCampaigns = $options['utm_campaigns'] ?? [];
            $utmMediums = $options['utm_mediums'] ?? [];

            sort($sources);
            sort($utmCampaigns);
            sort($utmMediums);

            return [
                'sources' => $sources,
                'utm_campaigns' => $utmCampaigns,
                'utm_mediums' => $utmMediums,
            ];
        });
    }
}

