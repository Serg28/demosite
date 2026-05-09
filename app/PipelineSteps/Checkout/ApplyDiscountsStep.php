<?php

namespace App\PipelineSteps\Checkout;

use App\Contracts\DiscountSource;
use App\DTO\Checkout\CheckoutContext;
use App\DTO\Checkout\DiscountBreakdown;
use Closure;

final class ApplyDiscountsStep
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        if (empty($context->appliedDiscounts)) {
            return $next($context);
        }

        $combinations = config('checkout.discount_combinations', []);
        $promoAmount = 0.0;
        $cardAmount = 0.0;

        $validated = $this->filterCompatible($context->appliedDiscounts, $combinations);

        foreach ($validated as $discount) {
            $amount = $discount->getAmount($context->subtotal);

            if ($discount->getType() === 'promo_code') {
                $promoAmount += $amount;
            } else {
                $cardAmount += $amount;
            }
        }

        $context->appliedDiscounts = $validated;
        $context->discountBreakdown = new DiscountBreakdown($promoAmount, $cardAmount);
        $context->discountTotal = $context->discountBreakdown->total;

        return $next($context);
    }

    /**
     * @param  DiscountSource[]  $discounts
     * @param  array<string, array<string, bool>>  $combinations
     * @return DiscountSource[]
     */
    private function filterCompatible(array $discounts, array $combinations): array
    {
        $result = [];

        foreach ($discounts as $discount) {
            $compatible = true;

            foreach ($result as $existing) {
                $rules = $combinations[$existing->getType()] ?? [];
                if (! ($rules[$discount->getType()] ?? false)) {
                    $compatible = false;
                    break;
                }
            }

            if ($compatible) {
                $result[] = $discount;
            }
        }

        return $result;
    }
}
