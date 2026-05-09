<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\Services\Delivery\DeliveryService;
use Closure;

final class CalculateDeliveryStep
{
    public function __construct(private readonly DeliveryService $deliveryService) {}

    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        if (! $context->delivery) {
            return $next($context);
        }

        $subtotalAfterDiscount = $context->subtotal - $context->discountTotal;

        $context->deliveryPrice = $this->deliveryService->isFree($context->delivery, $subtotalAfterDiscount)
            ? 0.0
            : $this->deliveryService->calculatePrice($context->delivery, $context);

        return $next($context);
    }
}
