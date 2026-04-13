<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Provider
    |--------------------------------------------------------------------------
    | Supported: "taqnyat", "unifonic", "msegat", "log"
    | Use "log" for development/testing (writes to laravel.log)
    */
    'provider' => env('SMS_PROVIDER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | SMS Sender Name
    |--------------------------------------------------------------------------
    */
    'sender_name' => env('SMS_SENDER_NAME', 'MIZAN'),

    /*
    |--------------------------------------------------------------------------
    | Provider Configurations
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'taqnyat' => [
            'api_url' => 'https://api.taqnyat.sa/v1/messages',
            'api_key' => env('SMS_TAQNYAT_API_KEY', ''),
        ],

        'unifonic' => [
            'api_url' => 'https://el.cloud.unifonic.com/rest/SMS/messages',
            'app_sid' => env('SMS_UNIFONIC_APP_SID', ''),
        ],

        'msegat' => [
            'api_url' => 'https://www.msegat.com/gw/sendsms.php',
            'api_key' => env('SMS_MSEGAT_API_KEY', ''),
            'username' => env('SMS_MSEGAT_USERNAME', ''),
        ],
    ],
];
