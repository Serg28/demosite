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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', '285599603178143'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', '684958ee2a5771f424367653d7fc2bfd'),
        'redirect' => env('FACEBOOK_URL_CALLBACK', 'https://shop.vis-design.com.ua/auth/facebook/callback'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', '191430752104-lhhpp1oidu1ktos79q3moi712vi4cgrd.apps.googleusercontent.com'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', 'OfbgJ5vrtOYYqVK6iGIlFVrt'),
        'redirect' => env('GOOGLE_URL_CALLBACK', 'https://shop.vis-design.com.ua/auth/google/callback'),
    ],

    'np' => [
        'api_key' => env('NP_API_KEY', 'c051502cbccf0692f7cd0596d44cef99'),
    ],

    'search' => [
        'hosts' => explode(',', env('ELASTICSEARCH_HOSTS')),
    ],

    'ukrposhta' => [
        'bearer' => env('SANDBOX_BEARER', 'f9027fbb-cf33-3e11-84bb-5484491e2c94'),
        'token' => env('SAND_COUNTERPARTY_TOKEN', 'ba5378df-985e-49c5-9cf3-d222fa60aa68'),
        'uuid' => env('SAND_COUNTERPARTY_UUID', '2304bbe5-015c-44f6-a5bf-3e750d753a17'),
    ],

    'justin' => [
        'api_key' => env('JUSTIN_API_KEY', '352fc5c8-1357-11ea-abdd-0050569b9e7e'),
        'login' => env('JUSTIN_LOGIN', 'user_DmitrenkoDI'),
        'password' => env('JUSTIN_PASSWORD', '9Ra5i3vP8J'),
    ],

    'easypay' => [
        'partner_key' => env('EASYPAY_PARTNER_KEY', 'easypay-test'),
        'service_key' => env('EASYPAY_SERVICE_KEY', 'MERCHANT-TEST'),
        'secret_key' => env('EASYPAY_SECRET_KEY', 'test'),
    ],

    'meest' => [
        'username' => env('MEEST_USERNAME', 'test_open_api'),
        'password' => env('MEEST_PASSWORD', 'wxHDvdlXyIo7'),
    ],

    //    'sales_drive' => [
    //        'is_send_order' => env('SALES_DRIVE_SEND_ORDER', false),
    //        'formcode' => env('SALES_DRIVE_FORMCODE', '0atIuewUgtY6mgGyXFFZTDkpAf_jeOKCKKHUzG9i4ArKCnX2gvZP8JhPCPPfm9yBIdzKCwxMhkI0KEf'),
    //        'cabinet_url' => env('SALES_DRIVE_CABINET_URL', 'https://mercurio.salesdrive.me'),
    //        'export_link' => env('SALES_DRIVE_EXPORT_LINK', 'https://mercurio.salesdrive.me/export/yml/export.yml?publicKey=aKEZxD9c1xgI3JreivV6-Ho2M_Vwt3mt6kvdPp6g6fSU7DxcMVdy3sEoRWjM')
    //    ],

    'checkbox_ua' => [
        'domain' => env('CHECKBOX_UA_DOMAIN', 'https://dev-api.checkbox.in.ua'),
        'login' => env('CHECKBOX_UA_LOGIN', 'testkey0266'),
        'password' => env('CHECKBOX_UA_PASSWORD', '123456'),
        'license_key' => env('CHECKBOX_UA_LICENSE_KEY', 'ab26ad16cadb404599640d39'),
    ],

    'api' => [
        'token' => env('API_KEY', '45438148-2108-4f0f-b80b-b122721a3e9f'),
    ],

    'prom' => [
        'token' => env('TOKEN_PROM', 'c02604a22be231e5f47c28c1e60997ed222a77f0'),
    ],

    'privat_pay_parts' => [
        'store_id' => env('PRIVAT_PAY_PARTS_STORE_ID', '4AAD1369CF734B64B70F'),
        'password' => env('PRIVAT_PAY_PARTS_PASSWORD', '75bef16bfdce4d0e9c0ad5a19b9940df'),
        'type'  => env('PRIVAT_PAY_PARTS_TYPE', 'PP'), //II - Мгновенная рассрочка; PP - Оплата частями; PB - Оплата частями. Деньги в периоде. IA - Мгновенная рассрочка. Акционная.
    ],

    'mono_pay_parts' => [
        'store_id' => env('MONO_PAY_PARTS_STORE_ID', '4AAD1369CF734B64B70F'),
        'secret' => env('MONO_PAY_PARTS_SECRET', '75bef16bfdce4d0e9c0ad5a19b9940df'),
        'mode' => env('MONO_MODE', 'Development'), //Development  //PreProduction //Production
    ],

    'mono_pay' => [
        'token' => env('MONO_PAY_TOKEN', 'xxxxxxxxxxxxxxxxxxxxxxx'),
    ],

    'alpha_sms' => [
        'api_key' => env('ALPHAS_SMS_API_KEY', 'test'),
        'sms_sender' => env('ALPHAS_SMS_SENDER', 'test'),
        'viber_sender' => env('ALPHAS_SMS_VIBER_SENDER', 'test'),
        'viber_lifetime' => env('ALPHAS_SMS_LIFETIME', 60),
        'viber_force_sms' => env('ALPHAS_SMS_FORCE_SMS', 0),
        'sms_send' => env('ALPHASMS_SMS_SEND', true),  //Отправлять смс
        'viber_send' => env('ALPHASMS_VIBER_SEND', true),  //Отправлять смс
    ],
];
