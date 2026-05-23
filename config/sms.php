<?php

return [
    'default' => env('SMS_DRIVER', 'log'),

    'drivers' => [
        'turbosms' => [
            'api_key' => env('TURBOSMS_API_KEY'),
            'sender'  => env('TURBOSMS_SENDER', 'Demo'),
        ],
        'vodafone' => [
            'token'  => env('VODAFONE_TOKEN'),
            'sender' => env('VODAFONE_SENDER', 'Demo'),
        ],
        'alphaname' => [
            'login'    => env('ALPHANAME_LOGIN'),
            'password' => env('ALPHANAME_PASSWORD'),
            'sender'   => env('ALPHANAME_SENDER', 'Demo'),
        ],
        'log' => [],
    ],
];
