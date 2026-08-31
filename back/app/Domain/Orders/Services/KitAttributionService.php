<?php

namespace App\Domain\Orders\Services;

use App\Domain\Users\Models\Trace;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Schema;

class KitAttributionService
{
    /**
     * Full page URL for orders.submission_url (admin shows path via OrderResource).
     */
    public function extractSubmissionUrl(array $input): ?string
    {
        $raw = $input['submission_url'] ?? null;
        if (!is_string($raw)) {
            return null;
        }
        $trimmed = trim($raw);

        return $trimmed === '' ? null : mb_substr($trimmed, 0, 2048);
    }

    /**
     * Persist UTM / referrer into traces for admin source badges and filters.
     */
    public function recordMarketingTrace(User $user, array $input): void
    {
        if (!Schema::hasTable('traces')) {
            return;
        }

        $source = $this->buildMarketingSource($input);
        if ($source === []) {
            return;
        }

        Trace::query()->create([
            'user_id' => $user->id,
            'hash' => bin2hex(random_bytes(20)),
            'source' => $source,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function buildMarketingSource(array $input): array
    {
        $keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'referrer'];
        $out = [];

        foreach ($keys as $key) {
            $raw = $input[$key] ?? null;
            if (!is_string($raw)) {
                continue;
            }
            $value = trim($raw);
            if ($value === '') {
                continue;
            }
            $out[$key] = mb_substr($value, 0, 500);
        }

        return $out;
    }
}
