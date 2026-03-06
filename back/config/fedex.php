<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FedEx mode
    |--------------------------------------------------------------------------
    |
    | If FEDEX_MODE_PROD=true – use production credentials,
    | otherwise use sandbox credentials.
    |
    */
    'mode' => env('FEDEX_MODE_PROD', false),

    /*
    |--------------------------------------------------------------------------
    | REST API credentials
    |--------------------------------------------------------------------------
    */
    'rest_key' => env('FEDEX_MODE_PROD', false)
        ? env('FEDEX_REST_KEY_PROD')
        : env('FEDEX_REST_KEY_SAND'),

    'rest_password' => env('FEDEX_MODE_PROD', false)
        ? env('FEDEX_REST_PASSWORD_PROD')
        : env('FEDEX_REST_PASSWORD_SAND'),

    /*
    |--------------------------------------------------------------------------
    | Account number
    |--------------------------------------------------------------------------
    */
    'account_number' => env('FEDEX_MODE_PROD', false)
        ? env('FEDEX_ACCOUNT_NUMBER_PROD')
        : env('FEDEX_ACCOUNT_NUMBER_SAND'),

    /*
    |--------------------------------------------------------------------------
    | Default options
    |--------------------------------------------------------------------------
    */
    'options' => [
        // Default shipper country (ISO code)
        'country' => env('FEDEX_DEFAULT_COUNTRY', 'US'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Parcel / label options
    |--------------------------------------------------------------------------
    |
    | These values are used in FedexService::getRecipientAddress()
    | and FedexService::createShippingLabel().
    | Adjust them according to real FedEx ship-to address and label settings.
    |
    */
    'parcel_options' => [
        'recipient_address' => [
            'street'       => env('FEDEX_RECIPIENT_STREET', '8627 NE 89th Ave'),
            'city'         => env('FEDEX_RECIPIENT_CITY', 'Vancouver'),
            'state'        => env('FEDEX_RECIPIENT_STATE', 'WA'),
            'postal_code'  => env('FEDEX_RECIPIENT_POSTAL', '98662'),
            'country'      => env('FEDEX_RECIPIENT_COUNTRY', 'US'),
        ],

        'recipient_name'  => env('FEDEX_RECIPIENT_NAME', 'Royal Element'),
        'recipient_phone' => env('FEDEX_RECIPIENT_PHONE', '5642377332'),

        // Total shipment weight object is taken from this config in FedexService
        'weigth' => [
            'units' => 'LB',
            'value' => env('FEDEX_PARCEL_WEIGHT', 1),
        ],
    ],
];


