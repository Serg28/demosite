<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;

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

        return DB::transaction(function () use ($order, $payload): bool {
            $invoice = $this->invoiceService->latestForOrder($order);
            if ($invoice) {
                $this->invoiceService->markPaid($invoice, $payload);
            }

            $order->update(['order_status_id' => 12]); // Оплачений

            return true;
        });
    }

    private function extractOrderId(array $payload): ?int
    {
        if (isset($payload['order_id'])) {
            if (is_numeric($payload['order_id'])) {
                return (int) $payload['order_id'];
            }

            // MonoPayParts: order_id є UUID → шукаємо invoice по gateway_response->mono_order_id
            $invoice = PaymentInvoice::where('gateway_response->mono_order_id', $payload['order_id'])->first();
            if ($invoice) {
                return $invoice->order_id;
            }
        }

        // MonoPay: reference = order_id як рядок
        if (isset($payload['reference']) && is_numeric($payload['reference'])) {
            return (int) $payload['reference'];
        }

        // WayForPay: orderReference = order_id як рядок
        if (isset($payload['orderReference']) && is_numeric($payload['orderReference'])) {
            return (int) $payload['orderReference'];
        }

        // PrivatBank Parts / EasyPay: orderId = order_id (числовий або "{id}_{time}" у PrivatBank)
        if (isset($payload['orderId'])) {
            $rawOrderId = (string) $payload['orderId'];
            if (is_numeric($rawOrderId)) {
                return (int) $rawOrderId;
            }
            // PrivatBank composite format: "{order_id}_{timestamp}"
            if (preg_match('/^(\d+)_/', $rawOrderId, $m)) {
                return (int) $m[1];
            }
        }

        // NovaPay: external_id = order_id (може бути на верхньому рівні або в payments[0])
        $externalId = $payload['external_id']
            ?? $payload['payments'][0]['external_id']
            ?? null;
        if ($externalId !== null && is_numeric($externalId)) {
            return (int) $externalId;
        }

        // Platon: order = order_id як рядок
        if (isset($payload['order']) && is_numeric($payload['order'])) {
            return (int) $payload['order'];
        }

        // LiqPay: order_id в закодованому полі data у форматі "{id}_{uniqid}"
        if (isset($payload['data'])) {
            $decoded = json_decode(base64_decode($payload['data']), true);
            $rawId = $decoded['order_id'] ?? null;
            if ($rawId) {
                return (int) explode('_', $rawId)[0];
            }
        }

        return null;
    }
}
