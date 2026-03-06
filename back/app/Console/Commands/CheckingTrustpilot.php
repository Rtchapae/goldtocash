<?php

namespace App\Console\Commands;

use App\Domain\Reviews\Models\Trustpilot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Command to fetch and store Trustpilot reviews
 *
 * REVIEW NOTES:
 * - Fetches reviews from Trustpilot.com using their JSON-LD structured data
 * - Parses HTML response to extract JSON-LD script tags
 * - Stores new reviews in database (skips duplicates by trustpilot_id)
 * - Uses Guzzle HTTP client (via Laravel Http facade) instead of file_get_contents
 * - Uses DOMDocument for HTML parsing instead of simple_html_dom library
 */
class CheckingTrustpilot extends Command
{
    protected $signature = 'order:checking_trustpilot';

    protected $description = 'Fetch and store Trustpilot reviews';

    private const TRUSTPILOT_URL = 'https://www.trustpilot.com/review/goldtocash.us';
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    public function handle(): int
    {
        $this->info('Fetching Trustpilot reviews...');

        try {
            // Fetch HTML page
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
            ])->timeout(30)->get(self::TRUSTPILOT_URL);

            if (!$response->successful()) {
                throw new Exception("Failed to fetch Trustpilot page: HTTP {$response->status()}");
            }

            $html = $response->body();

            // Extract JSON-LD data from HTML
            $jsonLdData = $this->extractJsonLdData($html);

            if (empty($jsonLdData)) {
                $this->warn('No JSON-LD data found in response');
                return Command::SUCCESS;
            }

            // Process reviews
            $newReviewsCount = $this->processReviews($jsonLdData);

            $this->info("Processed {$newReviewsCount} new review(s)");

            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('Failed to fetch Trustpilot reviews', [
                'command' => self::class,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Failed to fetch Trustpilot reviews: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Extract JSON-LD data from HTML
     *
     * REVIEW: Uses DOMDocument to parse HTML and find script tags with type="application/ld+json"
     * Returns the parsed JSON data structure
     *
     * @param string $html
     * @return array<string, mixed>
     * @throws Exception
     */
    private function extractJsonLdData(string $html): array
    {
        $dom = new \DOMDocument();

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Find all script tags with type="application/ld+json"
        $scripts = $xpath->query('//script[@type="application/ld+json"]');

        foreach ($scripts as $script) {
            $jsonContent = $script->nodeValue;
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to parse JSON-LD', [
                    'error' => json_last_error_msg(),
                ]);
                continue;
            }

            // Check if this is the data we need (contains @graph with Review items)
            if (isset($data['@graph']) && is_array($data['@graph'])) {
                return $data;
            }
        }

        return [];
    }

    /**
     * Process reviews from JSON-LD data
     *
     * REVIEW: Filters for Review type items, checks for duplicates,
     * and creates new Trustpilot records
     *
     * @param array<string, mixed> $jsonLdData
     * @return int Number of new reviews created
     */
    private function processReviews(array $jsonLdData): int
    {
        $newReviewsCount = 0;

        if (!isset($jsonLdData['@graph']) || !is_array($jsonLdData['@graph'])) {
            return 0;
        }

        foreach ($jsonLdData['@graph'] as $data) {
            // Skip if not a Review type
            if (!isset($data['@type']) || $data['@type'] !== 'Review') {
                continue;
            }

            // Skip if missing required fields
            if (!isset($data['@id']) || !isset($data['author']) || !isset($data['reviewRating'])) {
                continue;
            }

            // Skip if review already exists
            if (Trustpilot::where('trustpilot_id', $data['@id'])->exists()) {
                continue;
            }

            try {
                Trustpilot::create([
                    'trustpilot_id' => $data['@id'],
                    'author_name' => $data['author']['name'] ?? null,
                    'author_type' => $data['author']['@type'] ?? null,
                    'author_url' => $data['author']['url'] ?? null,
                    'title' => $data['headline'] ?? null,
                    'review' => $data['reviewBody'] ?? null,
                    'rating' => $data['reviewRating']['ratingValue'] ?? null,
                    'date_published' => isset($data['datePublished'])
                        ? Carbon::parse($data['datePublished'])
                        : now(),
                ]);

                $newReviewsCount++;
                $this->line("Added review: {$data['@id']}");
            } catch (Exception $e) {
                Log::warning('Failed to create Trustpilot review', [
                    'trustpilot_id' => $data['@id'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $newReviewsCount;
    }
}
