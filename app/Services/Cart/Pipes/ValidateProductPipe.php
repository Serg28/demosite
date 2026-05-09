<?php

namespace App\Services\Cart\Pipes;

use App\Services\Cart\CartContext;
use App\Services\Cart\Contracts\CartPipeInterface;
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
