<?php

namespace Tests\Feature\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\DTO\Checkout\DiscountBreakdown;
use App\DTO\Checkout\TaxBreakdown;
use App\Models\DiscountCard;
use App\Models\PayMethod;
use App\Models\PromoCode;
use App\PipelineSteps\Checkout\ApplyDiscounts;
use App\PipelineSteps\Checkout\CalculateTotals;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\Strategies\FlatCommissionStrategy;
use App\Services\Payment\Strategies\MonoPartsCommissionStrategy;
use Tests\TestCase;

class CheckoutPipelineTest extends TestCase
{
    public function test_tax_breakdown_total_is_sum_of_parts(): void
    {
        $tax = new TaxBreakdown(productTax: 100.0, paymentCommission: 25.0);
        $this->assertEquals(125.0, $tax->total);
    }

    public function test_discount_breakdown_total_is_sum_of_parts(): void
    {
        $discount = new DiscountBreakdown(promoCode: 50.0, discountCard: 30.0);
        $this->assertEquals(80.0, $discount->total);
    }

    public function test_checkout_context_initializes_correctly(): void
    {
        $context = new CheckoutContext;
        $this->assertFalse($context->hasFailed());
        $this->assertEquals(0.0, $context->subtotal);
        $this->assertEquals('per_product', $context->taxMode);
    }

    public function test_context_fail_sets_failed_flag(): void
    {
        $context = new CheckoutContext;
        $context->fail('Помилка тесту');
        $this->assertTrue($context->hasFailed());
        $this->assertEquals('Помилка тесту', $context->failReason);
    }

    public function test_calculate_totals_step(): void
    {
        $context = new CheckoutContext;
        $context->subtotal = 1000.0;
        $context->discountTotal = 100.0;
        $context->deliveryPrice = 50.0;
        $context->taxes = new TaxBreakdown(productTax: 0.0, paymentCommission: 20.0);
        $context->giftCertificateTotal = 0.0;

        $step = new CalculateTotals;
        $result = $step->handle($context, fn ($ctx) => $ctx);

        $this->assertEquals(970.0, $result->total);
        $this->assertEquals(970.0, $result->paymentAmount);
        $this->assertFalse($result->isPaidByCertificates);
    }

    public function test_calculate_totals_with_gift_certificates(): void
    {
        $context = new CheckoutContext;
        $context->subtotal = 500.0;
        $context->discountTotal = 0.0;
        $context->deliveryPrice = 0.0;
        $context->taxes = new TaxBreakdown(productTax: 0.0, paymentCommission: 0.0);
        $context->giftCertificateTotal = 500.0;

        $step = new CalculateTotals;
        $result = $step->handle($context, fn ($ctx) => $ctx);

        $this->assertEquals(500.0, $result->total);
        $this->assertEquals(0.0, $result->paymentAmount);
        $this->assertTrue($result->isPaidByCertificates);
    }

    public function test_promo_code_percent_discount(): void
    {
        $promo = new PromoCode(['code' => 'TEST10', 'type' => 'percent', 'value' => 10.0]);
        $this->assertEquals(100.0, $promo->getAmount(1000.0));
        $this->assertEquals('promo_code', $promo->getType());
    }

    public function test_promo_code_fixed_cannot_exceed_subtotal(): void
    {
        $promo = new PromoCode(['code' => 'BIG500', 'type' => 'fixed', 'value' => 500.0]);
        $this->assertEquals(100.0, $promo->getAmount(100.0));
    }

    public function test_discount_card_percent(): void
    {
        $card = new DiscountCard(['code' => 'DC-001', 'type' => 'percent', 'value' => 5.0]);
        $this->assertEquals(100.0, $card->getAmount(2000.0));
        $this->assertEquals('discount_card', $card->getType());
    }

    public function test_apply_discounts_step_handles_empty(): void
    {
        $context = new CheckoutContext;
        $context->subtotal = 500.0;
        $step = new ApplyDiscounts;
        $result = $step->handle($context, fn ($ctx) => $ctx);
        $this->assertEquals(0.0, $result->discountTotal);
    }

    public function test_flat_commission_strategy(): void
    {
        $payMethod = new PayMethod(['commission_percent' => 2.5]);
        $strategy = new FlatCommissionStrategy;
        $commission = $strategy->calculate(1000.0, ['pay_method' => $payMethod]);
        $this->assertEquals(25.0, $commission);
    }

    public function test_mono_parts_commission_strategy(): void
    {
        $strategy = new MonoPartsCommissionStrategy;
        $commission = $strategy->calculate(1000.0, ['months' => 6]);
        $this->assertEquals(34.90, $commission);
    }

    public function test_gateway_registry_throws_for_unknown(): void
    {
        $registry = new GatewayRegistry;
        $this->expectException(\InvalidArgumentException::class);
        $registry->resolve('unknown_gateway');
    }
}
