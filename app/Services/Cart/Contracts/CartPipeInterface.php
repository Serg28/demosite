<?php

namespace App\Services\Cart\Contracts;

use App\Services\Cart\CartContext;
use Closure;

interface CartPipeInterface
{
    public function handle(CartContext $context, Closure $next): CartContext;
}
