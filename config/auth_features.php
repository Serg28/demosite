<?php

return [
    'registration'    => env('AUTH_REGISTRATION', true),
    'two_factor'      => env('AUTH_2FA', true),
    'password_reset'  => env('AUTH_PASSWORD_RESET', true),
    'email_verify'    => env('AUTH_EMAIL_VERIFY', false),
    'socialite'       => [
        'google'   => env('SOCIALITE_GOOGLE', false),
        'facebook' => env('SOCIALITE_FACEBOOK', false),
    ],
    'otp'             => [
        'enabled' => env('AUTH_OTP', false),
        'channel' => env('OTP_CHANNEL', 'email'), // 'email' | 'sms'
    ],
];
