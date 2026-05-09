<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pipeline кроки Checkout
    |--------------------------------------------------------------------------
    | Порядок важливий. Кожен крок реалізує handle(CheckoutContext, Closure).
    */
    'pipeline' => [
        \App\PipelineSteps\Checkout\ValidateCartStep::class,
        \App\PipelineSteps\Checkout\ApplyDiscountsStep::class,
        \App\PipelineSteps\Checkout\ValidateGiftCertificatesStep::class,
        \App\PipelineSteps\Checkout\CalculateDeliveryStep::class,
        \App\PipelineSteps\Checkout\CalculateCommissionStep::class,
        \App\PipelineSteps\Checkout\CalculateTotalsStep::class,
        \App\PipelineSteps\Checkout\CreateOrderStep::class,
        \App\PipelineSteps\Checkout\ProcessPaymentStep::class,
        \App\PipelineSteps\Checkout\ClearCartStep::class,
        \App\PipelineSteps\Checkout\SendNotificationsStep::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Режим знижок
    |--------------------------------------------------------------------------
    | per_product — знижка розподілена по товарах (для 1С-сумісності)
    | order_level — знижка = окремий рядок у підсумку
    */
    'discount_mode' => env('CHECKOUT_DISCOUNT_MODE', 'per_product'),

    /*
    |--------------------------------------------------------------------------
    | Режим податку (НДС)
    |--------------------------------------------------------------------------
    | per_product — НДС у CartItem (пакет linecore/shoppingcart)
    | order_level — НДС = окремий рядок, taxRate=0 у CartItem
    */
    'tax_mode' => env('CHECKOUT_TAX_MODE', 'per_product'),

    /*
    |--------------------------------------------------------------------------
    | Сумісність знижок
    |--------------------------------------------------------------------------
    | true  = можна комбінувати, false = взаємовиключають
    */
    'discount_combinations' => [
        'promo_code'    => ['discount_card' => true],
        'discount_card' => ['promo_code' => true],
    ],

    /*
    |--------------------------------------------------------------------------
    | Резервування подарункових сертифікатів
    |--------------------------------------------------------------------------
    | TTL резерву в Cache (секунди), ключ gift:reserve:{code}
    */
    'gift_certificate_reserve_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Стратегія сгорання сертифікатів
    |--------------------------------------------------------------------------
    | burn_all     — залишок балансу сгорає (default)
    | keep_remaining — залишок залишається на сертифікаті
    */
    'gift_certificate_burn_strategy' => env('GIFT_CERTIFICATE_BURN_STRATEGY', 'burn_all'),
];
