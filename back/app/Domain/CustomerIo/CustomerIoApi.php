<?php

namespace App\Domain\CustomerIo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class CustomerIoApi
{
    private Client $http;

    private string $baseUrl;

    private string $basicAuth;

    public function __construct()
    {
        $this->baseUrl = 'https://track.customer.io/api';
        $this->http = new Client();
        $siteId = config('services.customer_dot_io.site_id', '');
        $apiKey = config('services.customer_dot_io.api_key', '');
        $this->basicAuth = base64_encode($siteId . ':' . $apiKey);
    }

    public function request(string $method, string $path, array $options = []): ?ResponseInterface
    {
        $start = microtime(true);
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => 'Basic ' . $this->basicAuth,
        ]);

        try {
            $response = $this->http->request($method, $this->baseUrl . $path, $options);
        } catch (GuzzleException $e) {
            $response = $e instanceof \GuzzleHttp\Exception\BadResponseException
                ? $e->getResponse()
                : null;
            Log::warning('Customer.io API request failed', [
                'signature' => self::class,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);
        }

        Log::info('customer.io request finished', [
            'signature' => self::class,
            'path' => $path,
            'status' => $response ? $response->getStatusCode() : null,
            'durationInSeconds' => round(microtime(true) - $start, 1),
        ]);

        return $response;
    }
}
