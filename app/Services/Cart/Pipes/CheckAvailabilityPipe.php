<?php

namespace App\Services\Cart\Pipes;

use App\Services\Cart\CartContext;
use App\Services\Cart\CartService;
use App\Services\Cart\Contracts\CartPipeInterface;
use Closure;

final class CheckAvailabilityPipe implements CartPipeInterface
{
    public function __construct(private readonly CartService $cartService) {}

    public function handle(CartContext $context, Closure $next): CartContext
    {
        $available = $this->cartService->availableForOrder(
            $context->product,
            $context->requestedQty,
            'add',
        );

        if ($available <= 0) {
            $context->fail(__t('Досягнуто максимальну кількість'));

            return $context;
        }

        $context->setResolvedQty($available);

        return $next($context);
    }
}
