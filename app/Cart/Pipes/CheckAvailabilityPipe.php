<?php

namespace App\Cart\Pipes;

use App\Cart\CartContext;
use App\Cart\Contracts\CartPipeInterface;
use App\Services\CartService;
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
