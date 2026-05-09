<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\GatewayRegistry;

class WebhookProcessor
{
    public function __construct(
        private readonly GatewayRegistry $registry,
        private readonly PaymentInvoiceService $invoiceService,
    ) {}

    public function process(string $gatewaySlug, array $payload): bool
    {
        $gateway = $this->registry->resolve($gatewaySlug);
        $confirmed = $gateway->confirm($payload);

        if (! $confirmed) {
            return false;
        }

        $orderId = $this->extractOrderId($payload);
        if (! $orderId) {
            return false;
        }

        $order = Order::find($orderId);
        if (! $order) {
            return false;
        }

        $invoice = $this->invoiceService->latestForOrder($order);
        if ($invoice) {
            $this->invoiceService->markPaid($invoice, $payload);
        }

        $order->update(['order_status_id' => 12]); // Оплачений

        return true;
    }

    private function extractOrderId(array $payload): ?int
    {
        if (isset($payload['order_id'])) {
            return (int) $payload['order_id'];
        }

        // LiqPay: order_id знаходиться в закодованому полі data у форматі "{id}_{uniqid}"
        if (isset($payload['data'])) {
            $decoded = json_decode(base64_decode($payload['data']), true);
            $rawId   = $decoded['order_id'] ?? null;
            if ($rawId) {
                return (int) explode('_', $rawId)[0];
            }
        }

        return null;
    }
}
