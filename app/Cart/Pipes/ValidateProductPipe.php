<?php

namespace App\Cart\Pipes;

use App\Cart\CartContext;
use App\Cart\Contracts\CartPipeInterface;
use Closure;

final class ValidateProductPipe implements CartPipeInterface
{
    public function handle(CartContext $context, Closure $next): CartContext
    {
        if (! $context->product->isActiveForOrder()) {
            $context->fail(__t('Товар недоступний для замовлення'));

            return $context;
        }

        return $next($context);
    }
}
