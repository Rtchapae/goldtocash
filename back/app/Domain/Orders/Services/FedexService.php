<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Dto\FedExLocationDto;
use App\Domain\Users\Models\User;
use App\Domain\Orders\Services\ImageService;
use FedexRest\Authorization\Authorize;
use FedexRest\Entity\Address;
use FedexRest\Services\AddressValidation\AddressValidation;
use FedexRest\Services\Location\LocationSearchRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class FedexService
{
    protected ?Address $addressShipper = null;
    protected ?Address $addressRecipient = null;
    protected $authorize = null;
    protected ?bool $fedexMode = null;

    protected function getAuthorization()
    {
        if ($this->authorize !== null) {
            return $this->authorize;
        }

        $clientId = Config::get('fedex.rest_key');
        $clientSecret = Config::get('fedex.rest_password');

        if (empty($clientId) || empty($clientSecret)) {
            throw new Exception('FedEx credentials are not configured. Please set FEDEX_REST_KEY and FEDEX_REST_PASSWORD in your .env file.');
        }

        $this->fedexMode = Config::get('fedex.mode');
        $this->authorize = (new Authorize)
            ->when($this->fedexMode, function ($query) {
                return $query->useProduction();
            })
            ->setClientId($clientId)
            ->setClientSecret($clientSecret)
            ->authorize();

        return $this->authorize;
    }

    protected function getRecipientAddress(): Address
    {
        if ($this->addressRecipient !== null) {
            return $this->addressRecipient;
        }

        $this->addressRecipient = (new Address())
            ->setStreetLines(Config::get('fedex.parcel_options.recipient_address.street'))
            ->setCity(Config::get('fedex.parcel_options.recipient_address.city'))
            ->setStateOrProvince(Config::get('fedex.parcel_options.recipient_address.state'))
            ->setPostalCode(Config::get('fedex.parcel_options.recipient_address.postal_code'))
            ->setCountryCode(Config::get('fedex.parcel_options.recipient_address.country'));

        return $this->addressRecipient;
    }

    public function validateAddress(array $params): bool
    {
        try {
            $address = $params['address'] ?? '';
            if (strlen($address) > 34) {
                $offset = strpos($address, ' ', strlen($address) / 2);
                $streetLines = [
                    substr($address, 0, $offset),
                    substr($address, $offset + 1)
                ];
            } else {
                $streetLines = [$address];
            }

            $countryCode = Config::get('fedex.options.country') ?: 'US';

            $this->addressShipper = (new Address())
                ->setStreetLines(...$streetLines)
                ->setCity(isset($params['city']) && !empty($params['city']) ? $params['city'] : '')
                ->setStateOrProvince(isset($params['state']) && !empty($params['state']) ? $params['state'] : '')
                ->setPostalCode(isset($params['zip']) && !empty($params['zip']) ? $params['zip'] : '')
                ->setCountryCode($countryCode);

            $authorize = $this->getAuthorization();

            $response = (new AddressValidation())
                ->when($this->fedexMode, function ($query) {
                    return $query->useProduction();
                })
                ->setAddress($this->addressShipper)
                ->setAccessToken($authorize->access_token)
                ->request();

        } catch (\Exception $e) {
            Log::critical('Exception while validating address', [
                'signature' => 'Domain.Orders.Services.FedexService',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'address' => [
                    'streetLines' => $streetLines ?? null,
                ],
            ]);
            return false;
        }

        return $response->output->resolvedAddresses[0]->classification != 'UNKNOWN';
    }

    protected function fixPackageLineItemsWeight(array $packageLineItems): array
    {
        foreach ($packageLineItems as &$item) {
            if (isset($item['weight']['value']) && is_string($item['weight']['value'])) {
                $item['weight']['value'] = (float) $item['weight']['value'];
            }
        }
        return $packageLineItems;
    }

    public function createShippingLabel(User $user): array
    {
        try {
            if ($this->addressShipper === null) {
                throw new Exception('Shipper address is not set. Please validate address first.');
            }

            $shipper = (new \FedexRest\Entity\Person())
                ->withAddress($this->addressShipper)
                ->setPersonName($user->last_name . ' ' . $user->first_name)
                ->setPhoneNumber(intval(
                    preg_replace('/[^0-9]/', '', $user->phone)
                ))
                ->prepare();

            $recipient = (new \FedexRest\Entity\Person())
                ->withAddress($this->getRecipientAddress())
                ->setPersonName(Config::get('fedex.parcel_options.recipient_name'))
                ->setPhoneNumber(intval(
                    preg_replace('/[^0-9]/', '', Config::get('fedex.parcel_options.recipient_phone'))
                ))
                ->prepare();

            $tagRequest = new \FedexRest\Services\Ship\CreateTagRequest();

            $items = (new \FedexRest\Entity\Item())
                ->setItemDescription('jewelry')
                ->setWeight((new \FedexRest\Entity\Weight())->setValue(1.0)->setUnit('LB'));

            $weightConfig = Config::get('fedex.parcel_options.weigth');

            if (is_array($weightConfig) && isset($weightConfig['value'])) {
                $totalWeight = (float) $weightConfig['value'];
            } else {
                $totalWeight = $weightConfig ?? 1;
            }

            $params = [
                'json' => [
                    'labelResponseOptions' => 'LABEL',
                    'requestedShipment' => [
                        'shipper' => $shipper,
                        'recipients' => array($recipient),
                        'shipDatestamp' => \Carbon\Carbon::now()->toDateString(),
                        'serviceType' => 'FEDEX_EXPRESS_SAVER',
                        'packagingType' => 'FEDEX_ENVELOPE',
                        'pickupType' => 'USE_SCHEDULED_PICKUP',
                        'blockInsightVisibility' => false,
                        'totalWeight' => $totalWeight,
                        'shippingChargesPayment' => (object) [
                            'paymentType' => 'RECIPIENT',
                            'payor' => (object) [
                                'responsibleParty' => (object) [
                                    'accountNumber' => (object) [
                                        'value' => Config::get('fedex.account_number'),
                                    ],
                                ],
                            ],
                        ],
                        'shipmentSpecialServices' => [
                            'specialServiceTypes' => [
                                'HOLD_AT_LOCATION',
                            ],
                            'holdAtLocationDetail' => [
                                'locationId' => 'MRIKI', // Location id for 330 E Mill Plain Blvd Suite 100, Vancouver, WA 98660
                            ],
                        ],
                        'labelSpecification' => [
                            'imageType' => 'PNG',
                            'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL',
                        ],
                        'requestedPackageLineItems' => $this->fixPackageLineItemsWeight($items->prepare()),
                    ],
                    'accountNumber' => [
                        'value' => Config::get('fedex.account_number'),
                    ],
                ],
            ];

            $jsonRequest = json_encode($params['json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            $authorize = $this->getAuthorization();

            $response = $tagRequest
                ->when($this->fedexMode, function ($query) {
                    return $query->useProduction();
                })
                ->setRequestParams($params)
                ->setAccessToken($authorize->access_token)
                ->request();

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            $bodyContent = $response ? $response->getBody()->getContents() : 'No response body';
            $body = json_decode($bodyContent, true);

            Log::error('FedEx BadResponseException', [
                'message' => $e->getMessage(),
                'status_code' => $response ? $response->getStatusCode() : null,
                'response_body' => $body,
                'errors' => $body['errors'] ?? null,
                'full_response' => $bodyContent,
                'line' => $e->getLine(),
            ]);

            if (isset($body['errors'][0])) {
                $error = $body['errors'][0];
                $errorCode = $error['code'] ?? null;
                $errorMessage = $error['message'] ?? 'Unknown error';
                $parameterList = $error['parameterList'] ?? null;

                if ($errorCode === "HAL.LOCATIONID.INVALID") {
                    throw new Exception('Error! Connection problem found...', 4000);
                }

                Log::error('FedEx API Error', [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                    'parameterList' => $parameterList,
                    'full_error' => $error,
                ]);

                $errorDetails = $errorMessage;
                if ($parameterList && is_array($parameterList)) {
                    $errorDetails .= ' (Parameters: ' . json_encode($parameterList) . ')';
                }

                throw new Exception("FedEx API Error: {$errorDetails} (Code: {$errorCode})", 4001);
            }

            throw new Exception('Error! Connection problem found...', 4001);

        } catch (\Exception $e) {
            Log::critical('FedEx exception while creating label', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Error! Connection problem found...', 400);
        }

        $buffer = $response->getBody()->getContents();
        $fedexOrder = json_decode($buffer);

        if (isset($fedexOrder->output->transactionShipments[0]->completedShipmentDetail->completedPackageDetails[0]->label)) {
            $fedexOrder->output->transactionShipments[0]->completedShipmentDetail->completedPackageDetails[0]->label = 'DROPPED_POST_RESPONSE_BY_CLIENT';
        }

        $fedexOrderElement = array_shift($fedexOrder->output->transactionShipments);
        $track_number = $fedexOrderElement->masterTrackingNumber;
        $label = $fedexOrderElement->pieceResponses[0]->packageDocuments[0]->encodedLabel;

        $imageService = new ImageService();
        $label = $imageService->cropImageFromBase64($label);

        return [
            'track_number' => $track_number,
            'label' => $label,
            'fedex_order' => $fedexOrder,
        ];
    }

    public function getTrackingStatuses(array $trackingNumbers): array
    {
        if (empty($trackingNumbers)) {
            return [];
        }

        $results = [];

        foreach ($trackingNumbers as $trackingNumber) {
            if (empty($trackingNumber)) {
                continue;
            }

            try {
                $authorize = $this->getAuthorization();

                $response = (new \FedexRest\Services\Track\TrackByTrackingNumberRequest())
                    ->when($this->fedexMode, function ($query) {
                        return $query->useProduction();
                    })
                    ->setTrackingNumber($trackingNumber)
                    ->setAccessToken($authorize->access_token)
                    ->request();

                if (is_object($response) && method_exists($response, 'getBody')) {
                    $body = json_decode($response->getBody()->getContents(), true);
                } elseif (is_object($response) || is_array($response)) {
                    $body = json_decode(json_encode($response), true);
                } else {
                    continue;
                }

                $trackResult = $body['output']['completeTrackResults'][0]['trackResults'][0] ?? null;
                if (!$trackResult) {
                    continue;
                }

                if (!empty($trackResult['error'])) {
                    $err = $trackResult['error'];
                    continue;
                }

                if (isset($trackResult['latestStatusDetail'])) {
                    $statusDetail = $trackResult['latestStatusDetail'];
                    $derivedCode = $statusDetail['derivedCode'] ?? null;
                    $description = $statusDetail['description'] ?? '';

                    $status = new class($derivedCode, $description) {
                        private ?string $derivedCode;
                        private string $description;

                        public function __construct(?string $derivedCode, string $description)
                        {
                            $this->derivedCode = $derivedCode;
                            $this->description = $description;
                        }

                        public function getDerivedCode(): ?string
                        {
                            return $this->derivedCode;
                        }

                        public function indicatesInTransit(): bool
                        {
                            if (!$this->derivedCode) {
                                return false;
                            }

                            $statusBlacklist = ['IN', 'OC', 'CA', 'PD', 'PM', 'SE', 'SP', 'DL'];
                            return !in_array($this->derivedCode, $statusBlacklist);
                        }

                        public function indicatesDelivered(): bool
                        {
                            return $this->derivedCode === 'DL';
                        }
                    };

                    $results[$trackingNumber] = $status;
                } else {
                    Log::warning('Invalid tracking response structure', [
                        'tracking_number' => $trackingNumber,
                        'response' => $body,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to get tracking status', [
                    'tracking_number' => $trackingNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    public function getLocationsForZip(string $zip): array
    {
        $zipRaw = trim((string) $zip);
        $digits = preg_replace('/\D/', '', $zipRaw);
        $zip = strlen($digits) >= 5 ? substr($digits, 0, 5) : $zipRaw;
        if ($zip === '' || strlen($zip) < 5) {
            Log::info('FedEx getLocationsForZip: zip empty or too short', ['zip_original' => $zipRaw]);
            return [];
        }

        try {
            $authorize = $this->getAuthorization();

            $address = (new Address())
                ->setStreetLines(' ')
                ->setCity('')
                ->setStateOrProvince('')
                ->setPostalCode($zip)
                ->setCountryCode('US');

            $request = (new LocationSearchRequest())
                ->setAccessToken($authorize->access_token)
                ->setAddress($address)
                ->setResultsLimit(5)
                ->setDistance(50, LocationSearchRequest::UNITS_MI);

            if ($this->fedexMode) {
                $request->useProduction();
            }

            $response = $request->request();

            if (is_string($response)) {
                return [];
            }

            $data = json_decode(json_encode($response), true);
            if (!is_array($data)) {
                return [];
            }
            if (!empty($data['errors'])) {
                return [];
            }

            $output = $data['output'] ?? [];
            $list = $output['locationDetailList'] ?? $output['locationDetails'] ?? $output['locations']
                ?? $output['nearestLocations'] ?? $output['locationList'] ?? $output['results'] ?? $output['data']
                ?? $data['locationDetailList'] ?? $data['locationDetails'] ?? $data['locations']
                ?? $data['nearestLocations'] ?? $data['locationList'] ?? $data['results'] ?? $data['data'] ?? [];
            if (!is_array($list)) {
                $list = [];
            }
            if (count($list) === 0) {
                return [];
            }

            $locations = [];
            foreach ($list as $raw) {
                try {
                    $item = is_array($raw) ? $raw : (array) $raw;
                    if (count($item) === 1) {
                        $single = reset($item);
                        if (is_array($single)) {
                            $item = $single;
                        }
                    }
                    $locations[] = $this->parseLocationItem($item);
                } catch (\Throwable $e) {
                    Log::debug('FedEx location item parse skip', ['error' => $e->getMessage(), 'raw_keys' => is_array($raw) ? array_keys($raw) : []]);
                }
            }

            return $locations;
        } catch (\Exception $e) {
            Log::warning('FedEx getLocationsForZip failed', [
                'zip' => $zip,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    private function parseLocationItem(array $raw): FedExLocationDto
    {
        $contactAndAddress = $raw['contactAndAddress'] ?? $raw['locationContactAndAddress'] ?? [];
        $contact = is_array($contactAndAddress) ? ($contactAndAddress['contact'] ?? []) : [];
        $address = is_array($contactAndAddress) ? ($contactAndAddress['address'] ?? []) : [];
        if (empty($address)) {
            $address = $raw['address'] ?? [];
        }
        $distance = $raw['distance'] ?? [];
        $ancillary = is_array($contactAndAddress) ? ($contactAndAddress['addressAncillaryDetail'] ?? $contactAndAddress['addressAncillaryDetail'] ?? []) : [];

        $companyName = (is_array($contact) ? ($contact['companyName'] ?? null) : null)
            ?? $raw['locationDisplayName'] ?? $raw['companyName'] ?? null;
        $displayName = (is_array($ancillary) ? ($ancillary['displayName'] ?? null) : null)
            ?? $raw['displayName'] ?? null;
        $streetLines = $address['streetLines'] ?? $address['street_lines'] ?? [];
        $streetStr = is_array($streetLines) ? implode(' ', $streetLines) : (string) $streetLines;
        $distanceValue = isset($distance['value']) ? (float) $distance['value'] : null;
        if (isset($distance['units']) && $distance['units'] === 'KM' && $distanceValue !== null) {
            $distanceValue = round($distanceValue * 0.621371, 1);
        }
        $city = $address['city'] ?? '';
        $state = $address['stateOrProvinceCode'] ?? $address['state_or_province'] ?? $address['stateOrProvince'] ?? '';
        $postalCode = $address['postalCode'] ?? $address['postal_code'] ?? '';
        $countryCode = $address['countryCode'] ?? $address['country_code'] ?? 'US';

        return new FedExLocationDto(
            distanceInMiles: $distanceValue,
            companyName: $companyName,
            displayName: $displayName,
            streetLines: trim($streetStr),
            city: $city,
            state: $state,
            postalCode: $postalCode,
            countryCode: $countryCode,
        );
    }
}

