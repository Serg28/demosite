<?php

namespace App\Services\Payment\Gateways\MonoPay;

use App\Contracts\Holdable;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Returnable;
use App\Models\Order;
use App\Models\PaymentInvoice;
use App\Services\Payment\CredentialResolver;
use Illuminate\Support\Facades\Log;

class MonoPayGateway implements PaymentGatewayInterface, Holdable, Returnable
{
    public function __construct(private readonly CredentialResolver $credentials) {}

    /**
     * Ініціалізує hold-платіж через MonoPay.
     *
     * @return array{invoice_id: string, page_url: string}
     */
    public function init(Order $order): array
    {
        $client = $this->makeClient($order->pay_method_id);
        $result = $client->create([
            'amount'           => $this->toKopecks($order->getTotalCost()),
            'ccy'              => 980, // UAH
            'merchantPaymInfo' => [
                'reference'   => (string) $order->id,
                'destination' => __t('Оплата замовлення № ') . $order->id,
            ],
            'redirectUrl'      => route('checkout.success', $order),
            'webHookUrl'       => url('/payment/webhook/monopay'),
            'validity'         => 3600,
            'paymentType'      => 'hold',
        ]);

        return [
            'invoice_id' => $result['invoiceId'] ?? '',
            'page_url'   => $result['pageUrl'] ?? '',
        ];
    }

    /**
     * Перевіряє поточний статус оплати через MonoPay API.
     */
    public function status(PaymentInvoice $invoice): string
    {
        $invoiceId = $invoice->gateway_response['invoice_id'] ?? null;

        if (! $invoiceId) {
            return 'pending';
        }

        $invoice->loadMissing('order');
        $client   = $this->makeClient($invoice->order->pay_method_id);
        $response = $client->status($invoiceId);

        return $this->mapStatus($response?->status ?? '');
    }

    /**
     * Підтверджує webhook від MonoPay.
     * Якщо передані _raw_body + _x_sign — перевіряє RSA-підпис.
     *
     * @param  array{status: string, reference?: string, invoiceId?: string, _raw_body?: string, _x_sign?: string}  $payload
     */
    public function confirm(array $payload): bool
    {
        $status    = $payload['status'] ?? '';
        $rawBody   = $payload['_raw_body'] ?? null;

        if ($rawBody !== null) {
            $xSign     = $payload['_x_sign'] ?? '';
            $reference = $payload['reference'] ?? null;
            $order     = $reference ? Order::find((int) $reference) : null;

            if ($order) {
                $client = $this->makeClient($order->pay_method_id);

                if (! $client->verifyWebhookSignature($rawBody, $xSign)) {
                    Log::channel('payments')->warning('MonoPay: invalid webhook signature', [
                        'reference' => $reference,
                    ]);

                    return false;
                }
            }
        }

        return in_array($status, ['success', 'hold'], true);
    }

    /**
     * Підтверджує захоплення заблокованих коштів (hold → capture).
     */
    public function captureHold(Order $order): bool
    {
        $invoiceId = $this->resolveInvoiceId($order);

        if (! $invoiceId) {
            return false;
        }

        $client   = $this->makeClient($order->pay_method_id);
        $response = $client->finalize($invoiceId, $this->toKopecks($order->getTotalCost()));

        return $response !== null;
    }

    /**
     * Скасовує hold — кошти повертаються покупцю.
     */
    public function releaseHold(Order $order): bool
    {
        $invoiceId = $this->resolveInvoiceId($order);

        if (! $invoiceId) {
            return false;
        }

        $client   = $this->makeClient($order->pay_method_id);
        $response = $client->cancel($invoiceId);

        return $response !== null;
    }

    /**
     * Повертає кошти після успішної оплати (refund).
     */
    public function return(Order $order): bool
    {
        $invoiceId = $this->resolveInvoiceId($order);

        if (! $invoiceId) {
            return false;
        }

        $client   = $this->makeClient($order->pay_method_id);
        $response = $client->cancel($invoiceId);

        return $response !== null;
    }

    private function makeClient(int $payMethodId): MonoPayClient
    {
        $creds = $this->credentials->resolve($payMethodId);

        return new MonoPayClient($creds['token'] ?? '');
    }

    private function resolveInvoiceId(Order $order): ?string
    {
        $id = $order->paymentInvoices()
            ->latest()
            ->first()
            ?->gateway_response['invoice_id'] ?? null;

        return $id ?: null;
    }

    private function mapStatus(string $monoStatus): string
    {
        return match ($monoStatus) {
            'success'                          => 'paid',
            'hold'                             => 'hold',
            'processing'                       => 'processing',
            'failure', 'reversed', 'expired'   => 'failed',
            default                            => 'pending',
        };
    }

    private function toKopecks(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
