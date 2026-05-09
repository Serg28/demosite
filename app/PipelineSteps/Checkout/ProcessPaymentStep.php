<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\Services\GiftCertificateService;
use App\Services\Payment\GatewayRegistry;
use App\Services\Payment\PaymentInvoiceService;
use Closure;

final class ProcessPaymentStep
{
    public function __construct(
        private readonly GatewayRegistry $registry,
        private readonly PaymentInvoiceService $invoiceService,
        private readonly GiftCertificateService $certificateService,
    ) {}

    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        // Спочатку фіналізуємо сертифікати
        if (! empty($context->giftCertificateCodes)) {
            $this->certificateService->finalize($context->order, $context->giftCertificateCodes);
        }

        // Якщо сплачено повністю сертифікатами — gateway не потрібен
        if ($context->isPaidByCertificates) {
            return $next($context);
        }

        if (! $context->payMethod) {
            return $next($context);
        }

        $invoice = $this->invoiceService->create($context->order, $context->paymentAmount);

        $gateway = $this->registry->resolve($context->payMethod->slug);
        $redirectUrl = $gateway->init($context->order);

        $this->invoiceService->markInitiated($invoice, $redirectUrl);

        $context->paymentRedirectUrl = is_string($redirectUrl) ? $redirectUrl : null;

        return $next($context);
    }
}
