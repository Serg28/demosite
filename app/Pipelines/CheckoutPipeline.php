<?php

namespace App\Pipelines;

use App\DTO\Checkout\CheckoutContext;
use Illuminate\Pipeline\Pipeline;

class CheckoutPipeline
{
    public function __construct(private readonly Pipeline $pipeline) {}

    public function run(CheckoutContext $context): CheckoutContext
    {
        return $this->pipeline
            ->send($context)
            ->through(config('checkout.pipeline'))
            ->thenReturn();
    }
}
