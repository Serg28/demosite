<?php

namespace App\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;

class LiqPayCodGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    public function init(Order $order): string|array
    {
        $creds = $this->credentials->resolve($order->pay_method_id);
        // TODO: реалізація LiqPayCodGateway
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
