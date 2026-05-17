<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use Closure;

final class CalculateTotals
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        // total = (subtotal - discounts) + delivery + paymentCommission + productTax
        $context->total = round(
            ($context->subtotal - $context->discountTotal)
            + $context->deliveryPrice
            + $context->taxes->paymentCommission
            + $context->taxes->productTax,
            2,
        );

        // paymentAmount — скільки іде в gateway після сертифікатів
        $context->paymentAmount = max(0.0, round($context->total - $context->giftCertificateTotal, 2));
        $context->isPaidByCertificates = $context->paymentAmount === 0.0;

        return $next($context);
    }
}
