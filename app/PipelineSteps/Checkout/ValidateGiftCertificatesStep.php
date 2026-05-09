<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\Services\GiftCertificateService;
use Closure;

final class ValidateGiftCertificatesStep
{
    public function __construct(private readonly GiftCertificateService $service) {}

    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        if (empty($context->giftCertificateCodes)) {
            return $next($context);
        }

        $total = 0.0;

        foreach ($context->giftCertificateCodes as $code) {
            $certificate = $this->service->findValid($code);

            if (! $certificate) {
                $context->fail(__t('Сертифікат :code не знайдено або недійсний', ['code' => $code]));

                return $context;
            }

            if ($this->service->isReserved($code)) {
                $context->fail(__t('Сертифікат :code вже використовується', ['code' => $code]));

                return $context;
            }

            $this->service->reserve($code);
            $total += $certificate->amount;
        }

        $context->giftCertificateTotal = $total;

        return $next($context);
    }
}
