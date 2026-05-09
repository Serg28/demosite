<?php

namespace App\Services\Payment\Gateways\MonoPay;

use App\Contracts\Holdable;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Returnable;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;

class MonoPayGateway implements PaymentGatewayInterface, Holdable, Returnable
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    public function init(Order $order): string|array
    {
        $creds = $this->credentials->resolve($order->pay_method_id);
        // TODO: MonoPay init
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

    public function captureHold(Order $order): bool
    {
        return false;
    }

    public function releaseHold(Order $order): bool
    {
        return false;
    }

    public function return(Order $order): bool
    {
        return false;
    }
}
