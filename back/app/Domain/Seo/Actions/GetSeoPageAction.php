<?php

namespace App\Domain\Seo\Actions;

use App\Domain\Seo\Models\SeoPage;
use Illuminate\Support\Facades\Cache;

class GetSeoPageAction
{
    private const CACHE_TTL = 3600; // 1 hour

    public function execute(?string $routeName = null, ?string $pageUrl = null): ?SeoPage
    {
        $cacheKey = $this->getCacheKey($routeName, $pageUrl);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($routeName, $pageUrl) {
            if ($routeName) {
                return SeoPage::where('route_name', $routeName)
                    ->where('is_active', true)
                    ->first();
            }

            if ($pageUrl) {
                return SeoPage::where('page_url', $pageUrl)
                    ->where('is_active', true)
                    ->first();
            }

            return null;
        });
    }

    public function clearCache(?string $routeName = null, ?string $pageUrl = null): void
    {
        $cacheKey = $this->getCacheKey($routeName, $pageUrl);
        Cache::forget($cacheKey);
    }

    public function clearAllCache(): void
    {
        $redis = Cache::store('redis')->getRedis();
        $keys = $redis->keys('seo_page_*');
        if (!empty($keys)) {
            $redis->del($keys);
        }
    }

    private function getCacheKey(?string $routeName, ?string $pageUrl): string
    {
        if ($routeName) {
            return "seo_page_route_{$routeName}";
        }

        if ($pageUrl) {
            return "seo_page_url_" . md5($pageUrl);
        }

        return "seo_page_unknown";
    }
}
