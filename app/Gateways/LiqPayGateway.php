<?php

namespace App\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;

class LiqPayGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    public function init(Order $order): string|array
    {
        $creds = $this->credentials->resolve($order->pay_method_id);
        // TODO: реалізація LiqPay SDK
        return '';
    }

    public function status(PaymentInvoice $invoice): string
    {
        return 'pending';
    }

    public function confirm(array $payload): bool
    {
        return false;
    }
}
