<?php

namespace App\Cart\Contracts;

use App\Cart\CartContext;
use Closure;

interface CartPipeInterface
{
    public function handle(CartContext $context, Closure $next): CartContext;
}
