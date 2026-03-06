<?php

namespace App\Domain\CustomerIo;

use App\Domain\CustomerIo\Models\CustomerIoLead;
use Psr\Http\Message\ResponseInterface;

class CustomerIoService
{
    public const SERVICE_SLUG = 'customerio';

    public function __construct(
        private readonly CustomerIoApi $api
    ) {
    }

    public function serviceIsEnabled(): bool
    {
        if (! config('services.customer_dot_io.enabled', true)) {
            return false;
        }
        $siteId = config('services.customer_dot_io.site_id', '');
        $apiKey = config('services.customer_dot_io.api_key', '');

        return $siteId !== '' && $apiKey !== '';
    }

    public function identify(string $email): bool
    {
        if (! $this->serviceIsEnabled()) {
            return false;
        }
        $response = $this->api->request('POST', '/v2/entity', [
            'json' => [
                'type' => 'person',
                'identifiers' => ['email' => $email],
                'action' => 'event',
                'name' => 'identify',
                'timestamp' => now()->toISOString(),
            ],
        ]);
        if ($response && $response->getStatusCode() === 200) {
            CustomerIoLead::recordLastTransmission($email);
        }

        return $response !== null && $response->getStatusCode() === 200;
    }

    public function setValues(string $email, array $values): bool
    {
        if (! $this->serviceIsEnabled()) {
            return false;
        }
        $response = $this->api->request('PUT', '/v1/customers/' . rawurlencode($email), [
            'json' => $values,
        ]);
        if ($response && $response->getStatusCode() === 200) {
            CustomerIoLead::recordLastTransmission($email);
        }

        return $response !== null && $response->getStatusCode() === 200;
    }

    public function trackEvent(string $email, string $eventName): bool
    {
        if (! $this->serviceIsEnabled()) {
            return false;
        }
        $response = $this->api->request('POST', '/v2/entity', [
            'json' => [
                'type' => 'person',
                'identifiers' => ['email' => $email],
                'action' => 'event',
                'name' => $eventName,
                'timestamp' => now()->toISOString(),
            ],
        ]);
        if ($response && $response->getStatusCode() === 200) {
            CustomerIoLead::recordLastTransmission($email);
        }

        return $response !== null && $response->getStatusCode() === 200;
    }

    public function delete(string $email): bool
    {
        if (! $this->serviceIsEnabled()) {
            return false;
        }
        $response = $this->api->request('POST', '/v2/entity', [
            'json' => [
                'type' => 'person',
                'identifiers' => ['email' => $email],
                'action' => 'delete',
            ],
        ]);
        if ($response && $response->getStatusCode() === 200) {
            CustomerIoLead::query()->where('email', $email)->delete();
        }

        return $response !== null && $response->getStatusCode() === 200;
    }
}
