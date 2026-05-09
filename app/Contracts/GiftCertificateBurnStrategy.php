<?php

namespace App\Contracts;

use App\Models\GiftCertificate;

interface GiftCertificateBurnStrategy
{
    /**
     * Застосувати сертифікат до суми замовлення.
     * Повертає суму, яку покриває сертифікат.
     */
    public function apply(GiftCertificate $certificate, float $orderTotal): float;
}
