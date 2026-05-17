<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\DTO\Checkout\TaxBreakdown;
use App\Services\Payment\CommissionCalculator;
use Closure;

final class CalculateCommission
{
    public function __construct(private readonly CommissionCalculator $calculator) {}

    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        if (! $context->payMethod) {
            return $next($context);
        }

        $amountAfterDiscount = $context->subtotal - $context->discountTotal;

        $commission = $this->calculator->calculate(
            $context->payMethod,
            $amountAfterDiscount,
        );

        $context->taxes = new TaxBreakdown(
            productTax: $context->taxes->productTax,
            paymentCommission: $commission,
        );

        return $next($context);
    }
}
