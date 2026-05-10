<?php

return [
    'liqpay' => [
        'credentials' => [
            'public_key'  => 'services.liqpay.public_key',
            'private_key' => 'services.liqpay.private_key',
        ],
    ],

    'liqpay_cod' => [
        'credentials' => [
            'public_key'  => 'services.liqpay.public_key',
            'private_key' => 'services.liqpay.private_key',
        ],
    ],

    'monopay' => [
        'credentials' => [
            'token' => 'services.monopay.token',
        ],
    ],

    'monopayparts' => [
        'credentials' => [
            'token' => 'services.monopay.token',
        ],
    ],

    'wayforpay' => [
        'credentials' => [
            'merchant_account' => 'services.wayforpay.merchant_account',
            'merchant_secret'  => 'services.wayforpay.merchant_secret',
            'merchant_domain'  => 'services.wayforpay.merchant_domain',
        ],
    ],

    'privatpayparts' => [
        'credentials' => [
            'merchant_id' => 'services.privatbank.merchant_id',
            'password'    => 'services.privatbank.password',
        ],
    ],

    'easypay' => [
        'credentials' => [
            'account_id' => 'services.easypay.account_id',
            'secret_key' => 'services.easypay.secret_key',
        ],
    ],

    'novapay' => [
        'credentials' => [
            'merchant_id' => 'services.novapay.merchant_id',
            'private_key' => 'services.novapay.private_key',
        ],
    ],

    'paylink' => [
        'credentials' => [
            'api_key' => 'services.paylink.api_key',
        ],
    ],

    'platon' => [
        'credentials' => [
            'key'      => 'services.platon.key',
            'password' => 'services.platon.password',
        ],
    ],
];
