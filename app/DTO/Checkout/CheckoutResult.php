<?php

namespace App\DTO\Checkout;

use App\Models\Order;

readonly class CheckoutResult
{
    public function __construct(
        public bool $success,
        public ?Order $order,
        public ?string $redirectUrl,
        public ?string $errorMessage,
    ) {}

    public static function success(Order $order, ?string $redirectUrl = null): self
    {
        return new self(
            success: true,
            order: $order,
            redirectUrl: $redirectUrl,
            errorMessage: null,
        );
    }

    public static function failure(string $message): self
    {
        return new self(
            success: false,
            order: null,
            redirectUrl: null,
            errorMessage: $message,
        );
    }
}
