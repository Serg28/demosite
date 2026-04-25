<?php

namespace App\Services\Checkouts\LiqPay;

use App\Interfaces\Checkout;
use App\Models\Order;
use Arturishe21\LiqPay\LiqPay as LiqPayService;
use Illuminate\Support\Facades\Log;

class LiqPay implements Checkout
{
    private Order $order;

    private LiqPayService $liqPay;

    private string $privateKey;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->liqPay = new LiqPayService();
        $this->privateKey = config('liqpay.private_key');
    }

    public function init(): string
    {
        //$amount = $this->order->cost; //без доставки

        //Включить стоимость доставки, если она не оплачивается отдельно
        $amount = $this->order->price_delivery ?
            $this->order->price_delivery + $this->order->cost :
            $this->order->cost;

        $currency = 'UAH';
        $description = __t('Оплата на сайте '.env('APP_NAME'));
        $orderId = $this->order->id;

        $resultUrl = route('payment.result', $this->order);
        $serverUrl = route('payment.confirm', $this->order);

        return $this->liqPay->pay($amount, $currency, $description, $orderId, $resultUrl, $serverUrl);
    }

    public function status(): bool
    {
        return $this->liqPay->status($this->order->id);
    }

    public function confirm(): void
    {
        $data = request()->get('data');
        $liqSignature = request()->get('signature');

        $localSignature = base64_encode(sha1($this->privateKey.$data.$this->privateKey, 1));

        if ($localSignature === $liqSignature) {
            $this->order->update([
                'is_online_payed' => true,
            ]);

            return;
        }

        Log::error('Order Pay not confirmed.', request()->all());
    }
}
