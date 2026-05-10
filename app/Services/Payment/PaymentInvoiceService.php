<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentInvoice;

class PaymentInvoiceService
{
    public function create(Order $order, float $amount): PaymentInvoice
    {
        $existing = $order->paymentInvoices()
            ->whereIn('status', ['pending', 'initiated'])
            ->latest()
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return PaymentInvoice::create([
            'order_id' => $order->id,
            'amount'   => $amount,
            'status'   => 'pending',
        ]);
    }

    public function markInitiated(PaymentInvoice $invoice, string|array $gatewayResponse): void
    {
        $url = is_string($gatewayResponse) ? $gatewayResponse : ($gatewayResponse['url'] ?? null);

        $invoice->update([
            'status'           => 'initiated',
            'payment_url'      => $url,
            'gateway_response' => is_array($gatewayResponse) ? $gatewayResponse : ['url' => $gatewayResponse],
        ]);
    }

    public function markPaid(PaymentInvoice $invoice, array $payload = []): void
    {
        $invoice->update([
            'status'           => 'paid',
            'gateway_response' => array_merge($invoice->gateway_response ?? [], $payload),
            'paid_at'          => now(),
        ]);
    }

    public function markFailed(PaymentInvoice $invoice, string $reason, array $payload = []): void
    {
        $invoice->update([
            'status'           => 'failed',
            'fail_reason'      => $reason,
            'gateway_response' => array_merge($invoice->gateway_response ?? [], $payload),
        ]);
    }

    public function latestForOrder(Order $order): ?PaymentInvoice
    {
        return $order->paymentInvoices()->latest()->first();
    }
}
