<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateGoldPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-gold-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will fetch and cache the current price of gold per Troy OZ.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    private const CACHE_KEY_PRICE = 'gold-price';
    private const CACHE_KEY_LIMIT_HIT = 'gold-price-api-limit-hit';
    private const FALLBACK_PRICE = 5000;
    /** TTL for cached price: longer than schedule interval (3h) so value persists until next run */
    private const CACHE_TTL_MINUTES = 240; // 4 hours

    /** Last API error message for console output */
    protected ?string $lastApiError = null;

    public function handle()
    {
        $this->lastApiError = null;

        // Если лимит API уже исчерпан — не дергаем API 12 ч, используем fallback без логов
        if (Cache::get(self::CACHE_KEY_LIMIT_HIT)) {
            $price = Cache::get(self::CACHE_KEY_PRICE) ?? self::FALLBACK_PRICE;
            Cache::set(self::CACHE_KEY_PRICE, $price, now()->addMinutes(self::CACHE_TTL_MINUTES));
            $this->info("Using cached/fallback price (API limit exceeded): \${$price} / Troy Oz");
            return 0;
        }

        $price = $this->getUSDPerTroyOz();

        if (! $price) {
            $price = self::FALLBACK_PRICE;
            Log::warning('using mock gold price due to API failure', [
                'signature' => self::class,
                'price' => $price,
            ]);
            $this->warn('API failed, using fallback price.');
            if ($this->lastApiError !== null) {
                $this->error($this->lastApiError);
            }
            $this->info("Cached price: \${$price} / Troy Oz");
        } else {
            $this->info("Gold price updated: \${$price} / Troy Oz");
        }

        Log::info('updating gold price', [
            'signature' => self::class,
            'price' => $price,
        ]);
        Cache::set(self::CACHE_KEY_PRICE, $price, now()->addMinutes(self::CACHE_TTL_MINUTES));
        return 0;
    }

    public function getUSDPerTroyOz(): ?float
    {
        $client = new Client();

        $apiKey = config('services.metalprice.api_key');

        if (empty($apiKey)) {
            $this->lastApiError = 'METALPRICE_API_KEY is not set in .env';
            Log::warning('MetalPrice API key is missing (METALPRICE_API_KEY)', ['signature' => self::class]);
            return null;
        }

        try {
            $res = $client->get("https://api.metalpriceapi.com/v1/latest?api_key={$apiKey}&base=XAU&currencies=USD");
        } catch (\Throwable $e) {
            $this->lastApiError = 'Request failed: ' . $e->getMessage();
            Log::warning('MetalPrice API request failed', [
                'signature' => self::class,
                'message' => $e->getMessage(),
            ]);
            return null;
        }

        if ($res->getStatusCode() !== 200) {
            $this->lastApiError = 'API returned HTTP ' . $res->getStatusCode();
            Log::warning('MetalPrice API returned non-200', [
                'signature' => self::class,
                'statusCode' => $res->getStatusCode(),
            ]);
            return null;
        }

        $resBody = json_decode($res->getBody()->getContents(), true);
        $price = $resBody['rates']['USD'] ?? null;

        if ($price === null) {
            $statusCode = $resBody['error']['statusCode'] ?? null;
            $msg = $resBody['error']['message'] ?? (isset($resBody['error']) ? json_encode($resBody['error']) : 'Missing rates.USD');
            $this->lastApiError = 'MetalPrice API: ' . $msg;

            // 105 = monthly allowance exceeded — не вызывать API 12 ч, один раз пишем в лог
            if ((int) $statusCode === 105) {
                Cache::put(self::CACHE_KEY_LIMIT_HIT, true, now()->addHours(12));
                Log::warning('MetalPrice API monthly limit exceeded, skipping API calls for 12 hours', [
                    'signature' => self::class,
                ]);
            } else {
                Log::warning('MetalPrice API response missing rates.USD', [
                    'signature' => self::class,
                    'body' => $resBody,
                ]);
            }
        }

        return $price;
    }
}
