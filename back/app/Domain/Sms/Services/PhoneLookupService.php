<?php

namespace App\Domain\Sms\Services;

use App\Domain\Sms\DTOs\PhoneLookupDto;
use App\Domain\Sms\Models\SmsLookup;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Service for looking up phone numbers to determine if they are mobile
 *
 * REVIEW NOTES:
 * - Checks if phone number is mobile via external API
 * - Caches results in database to avoid repeated API calls
 * - Handles API failures gracefully by marking results as "assumed"
 * - Currently API is sunset, so all lookups will be assumed
 */
class PhoneLookupService
{
    /**
     * Lookup phone if needed (checks cache first)
     *
     * REVIEW: Checks database for existing lookup before making API call.
     * Returns cached result if available, otherwise performs new lookup.
     *
     * @param string $phone
     * @return PhoneLookupDto
     */
    public function lookupPhoneIfNeeded(string $phone): PhoneLookupDto
    {
        $existingLookup = SmsLookup::query()->where('phone', $phone)->first();

        if ($existingLookup) {
            return (new PhoneLookupDto())
                ->setPhone($phone)
                ->setIsMobile((bool) $existingLookup->is_mobile)
                ->setIsAssumed((bool) $existingLookup->is_assumed)
                ->setAssumptionReason($existingLookup->assumption_reason ?? '')
                ->setNeedsToBeStored(false);
        }

        return $this->lookupPhone($phone);
    }

    /**
     * Perform phone lookup via API
     *
     * REVIEW: Validates phone number format, then calls external API.
     * If API fails or phone is invalid, returns assumed result.
     *
     * @param string $phone
     * @return PhoneLookupDto
     */
    public function lookupPhone(string $phone): PhoneLookupDto
    {
        $dto = (new PhoneLookupDto())->setPhone($phone);

        if (!$this->isValidPhone($phone)) {
            Log::warning('phone was invalid', [
                'signature' => self::class,
                'phone' => $phone,
            ]);
            $dto->setIsMobile(false)
                ->setIsAssumed(false);
            return $dto;
        }

        $this->hitApi($dto);

        return $dto;
    }

    /**
     * Call external API to lookup phone
     *
     * REVIEW: Makes HTTP request to phone lookup API.
     * Currently API is sunset, so all requests will result in assumed values.
     * When API is available, updates DTO with actual mobile status.
     *
     * @param PhoneLookupDto $dto
     * @return void
     */
    private function hitApi(PhoneLookupDto $dto): void
    {
        Log::info('fetching phone lookup from api', [
            'signature' => self::class,
            'phone' => $dto->getPhone(),
        ]);

        try {
            // REVIEW: API has been sunset - all lookups will be assumed
            throw new \Exception('sms lookups have been sunset');

            // When API is available, uncomment and configure:
            // $client = new Client();
            // $response = $client->post('', [
            //     'timeout' => 15,
            //     'json' => [
            //         'phone' => $dto->getPhone(),
            //     ],
            // ]);
            // $responseArray = json_decode($response->getBody()->getContents(), true);
            // if ($responseArray['errorCode'] !== 'NO_ERROR') {
            //     throw new \Exception('service responded with an error code: ' . $responseArray['errorCode']);
            // }
            // $dto->setIsAssumed(false)
            //     ->setIsMobile(strtolower($responseArray['data']['Type']) === 'mobile');
        } catch (\Throwable $t) {
            Log::warning('exception while hitting phone lookup API', [
                'signature' => self::class,
                'error' => $t->getMessage(),
                'phone' => $dto->getPhone(),
            ]);

            $dto->setIsMobile(false)
                ->setIsAssumed(true)
                ->setAssumptionReason('exception while hitting endpoint: ' . $t->getMessage());
        }
    }

    /**
     * Validate phone number format
     *
     * REVIEW: Validates US phone number format (10 digits, no leading 0, 1, or +).
     * Used before making API calls to avoid invalid requests.
     *
     * @param string $phone
     * @return bool
     */
    private function isValidPhone(string $phone): bool
    {
        if (strlen($phone) !== 10) {
            return false;
        }

        if (in_array(substr($phone, 0, 1), ['+', '1', '0'])) {
            return false;
        }

        return !preg_match('/\D/', $phone);
    }
}


