<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Реєстр платіжних гейтвеїв
    |--------------------------------------------------------------------------
    | Додати новий гейтвей = 1 клас + 1 рядок тут
    */
    'gateways' => [
        'liqpay'           => \App\Gateways\LiqPayGateway::class,
        'liqpay_cod'       => \App\Gateways\LiqPayCodGateway::class,
        'monopay'          => \App\Gateways\MonoPayGateway::class,
        'monopayparts'     => \App\Gateways\MonoPayPartsGateway::class,
        'wayforpay'        => \App\Gateways\WayForPayGateway::class,
        'privatpayparts'   => \App\Gateways\PrivatPayPartsGateway::class,
        'easypay'          => \App\Gateways\EasyPayGateway::class,
        'novapay'          => \App\Gateways\NovaPayGateway::class,
        'paylink'          => \App\Gateways\PayLinkGateway::class,
        'platon'           => \App\Gateways\PlatonGateway::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Стратегії комісій для гейтвеїв
    |--------------------------------------------------------------------------
    */
    'commission_strategies' => [
        'monopayparts'   => \App\Services\Payment\MonoPartsCommissionStrategy::class,
        'privatpayparts' => \App\Services\Payment\PrivatPPCommissionStrategy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Дефолтна стратегія (flat відсоток)
    |--------------------------------------------------------------------------
    */
    'default_commission_strategy' => \App\Services\Payment\FlatCommissionStrategy::class,

    /*
    |--------------------------------------------------------------------------
    | Ротація ключів — таблиця payment_credentials
    |--------------------------------------------------------------------------
    | day_of_week: 1=Пн ... 7=Нд (ISO 8601)
    */
    'credential_rotation' => true,
];
