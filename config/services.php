<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'liqpay' => [
        'public_key'  => env('LIQPAY_PUBLIC_KEY'),
        'private_key' => env('LIQPAY_PRIVATE_KEY'),
    ],

    'monopay' => [
        'token' => env('MONOPAY_TOKEN'),
    ],

    'wayforpay' => [
        'merchant_account' => env('WAYFORPAY_MERCHANT_ACCOUNT'),
        'merchant_secret'  => env('WAYFORPAY_MERCHANT_SECRET'),
        'merchant_domain'  => env('WAYFORPAY_MERCHANT_DOMAIN'),
    ],

    'privatbank' => [
        'merchant_id' => env('PRIVATBANK_MERCHANT_ID'),
        'password'    => env('PRIVATBANK_PASSWORD'),
    ],

    'easypay' => [
        'account_id' => env('EASYPAY_ACCOUNT_ID'),
        'secret_key' => env('EASYPAY_SECRET_KEY'),
    ],

    'novapay' => [
        'merchant_id' => env('NOVAPAY_MERCHANT_ID'),
        'private_key' => env('NOVAPAY_PRIVATE_KEY'),
    ],

    'paylink' => [
        'api_key' => env('PAYLINK_API_KEY'),
    ],

    'platon' => [
        'key'      => env('PLATON_KEY'),
        'password' => env('PLATON_PASSWORD'),
    ],

    'checkbox_ua' => [
        'domain'      => env('CHECKBOX_DOMAIN'),
        'login'       => env('CHECKBOX_LOGIN'),
        'password'    => env('CHECKBOX_PASSWORD'),
        'license_key' => env('CHECKBOX_LICENSE_KEY'),
    ],

];
