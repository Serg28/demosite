<?php

namespace App\Actions\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\DTO\Checkout\CheckoutResult;
use App\Pipelines\CheckoutPipeline;
use Throwable;

class PlaceOrder
{
    public function __construct(private readonly CheckoutPipeline $pipeline) {}

    public function handle(CheckoutContext $context): CheckoutResult
    {
        try {
            $result = $this->pipeline->run($context);

            if ($result->hasFailed()) {
                return CheckoutResult::failure($result->failReason ?? __t('Помилка оформлення замовлення'));
            }

            return CheckoutResult::success($result->order, $result->paymentRedirectUrl);
        } catch (Throwable $e) {
            report($e);

            return CheckoutResult::failure(__t('Сталась помилка. Спробуйте ще раз.'));
        }
    }
}
