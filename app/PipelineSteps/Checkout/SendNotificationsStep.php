<?php

namespace App\PipelineSteps\Checkout;

use App\DTO\Checkout\CheckoutContext;
use App\Jobs\SendOrderNotification;
use Closure;

final class SendNotificationsStep
{
    public function handle(CheckoutContext $context, Closure $next): CheckoutContext
    {
        SendOrderNotification::dispatch($context->order)->onQueue('notifications');

        return $next($context);
    }
}
