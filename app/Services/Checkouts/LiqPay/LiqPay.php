<?php

namespace App\Services\Checkouts\LiqPay;

use App\Enums\AssemblyStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Interfaces\Checkout;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class LiqPay implements Checkout
{
    private Order $order;

    private LiqPayService $liqPay;

    public string $privateKey;

    public string $publicKey;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->liqPay = new LiqPayService();
        //$this->privateKey = config('liqpay.private_key');
        //$this->publicKey = config('liqpay.public_key');
    }

    //Считаем сумму предоплаты для данного метода оплаты
    public function getPrepaymentSum(): int|float|null
    {
        if ($this->order->payMethod->isPrepaymentActive()) {
            $amount = $this->order->getPriceForDocumentsAttribute();
            $prepayment_percent = $this->order->payMethod->prepayment_percent;

            // Рассчитываем сумму предоплаты на основе процента
            $prepayment_amount = round(($amount * $prepayment_percent) / 100);

            // Проверяем, не меньше ли сумма предоплаты минимальной суммы предоплаты
            $min_prepayment_amount = $this->order->payMethod->min_prepayment_amount;
            $prepayment_amount = max($prepayment_amount, $min_prepayment_amount);

            return $prepayment_amount;
        }

        return 0;
    }

    //Возвращаем сумму к оплате
    public function getAmountSum(): int|float|null
    {
        //Если активирована предоплата, возвращаем ее. Иначе - полную сумму заказа
        if ($this->order->payMethod->isPrepaymentActive()) {
            return $this->getPrepaymentSum();
        }
        return $this->order->getPriceForDocumentsAttribute();
    }


    public function init($action = 'hold'): string
    {
        $amount = $this->getAmountSum();

        if (empty($amount)) {
            return redirect()->route('checkout.complete');
        }

        //Используем уникальный номер заказа для LiqPay
        $orderId = $this->order->liqpay_order_id;
        if (empty($orderId)) {
            $orderId = $this->order->id . '_' . time();
            $this->order->update([
                'liqpay_order_id' => $orderId,
            ]);
        }
        //--

        $currency = 'UAH';
        $action = ($action) ?: 'hold'; //Холдирование
        //$description = __t('Оплата замовлення № ') . $this->order->id;
        $description = $this->getPrepaymentSum() ?
            __t('Оплата транспортных услуг на сайте').' '.config('app.name') :
            __t('Оплата замовлення № ') . $this->order->id;

        $resultUrl = route('payment.result', $this->order);
        $serverUrl = route('payment.confirm', $this->order);

        //return $this->liqPay->pay($amount, $currency, $description, $orderId, $resultUrl, $serverUrl);
        return $this->liqPay->pay($action, $amount, $currency, $description, $orderId, $resultUrl, $serverUrl);
    }

    public function status(): bool
    {
        return $this->liqPay->status($this->order->liqpay_order_id);
    }

    public function confirm(): void
    {
        $data = request()->post('data');
        Log::info('Order ' . $this->order->id . ' before confirm LiqPay: ', request()->all());
        $liqSignature = request()->post('signature');
        //$localSignature = base64_encode(sha1($this->privateKey . $data . $this->privateKey, 1));
        $localSignature = $this->liqPay->getLocaleSignature($data);

        if ($localSignature == $liqSignature) {
            $data = json_decode(base64_decode($data), true);
            switch ($data['status']) {
                case 'success':
                    $status_pay = PaymentStatusEnum::Paid(); //$status_pay = 1;
                    $fields['complect_status_id'] = AssemblyStatusEnum::BeingAssembled(); //$fields['complect_status_id'] = 3;
                    $this->createOrderPayment($data['amount']);
                    break;
                case 'hold_wait':
                    $status_pay = PaymentStatusEnum::Blocked(); //$status_pay = 2;
                    break;
                default:
                    $status_pay = PaymentStatusEnum::Unpaid(); //$status_pay = 0;
                    break;
            }
            $fields = [
                'is_online_payed' => $status_pay,
                'liqpay_info' => $data,
            ];
            $this->order->update($fields);

            return;
        }
        Log::error('Order ' . $this->order->id . ' LiqPay not confirmed.', request()->all());
    }

    public function createOrderPayment($price, $type = 'main_payment', $is_payed = 1)
    {
        return $this->order->payments()->create([
            'order_id' => $this->order->id,
            'legal_entities_recipient_id' => $this->order->legal_entities_recipient_id,
            'type' => $type,
            'price' => $price,
            'is_payed' => $is_payed
        ]);
    }

    /**
     * Получает LiqPay Order ID текущего заказа.
     *
     * Если $this->liqpay_info['order_id'] не пустой и равен $this->order->liqpay_order_id, возвращает $this->liqpay_info['order_id].
     * Если один из них пустой, возвращает непустое значение.
     * Если оба не пустые и различаются, возвращает $this->liqpay_info['order_id'].
     *
     * @return string|null LiqPay Order ID или null, если оба значения пусты.
     */
    public function getLiqpayOrderId(): ?string
    {
        $liqpayOrderId = $this->order->liqpay_info['order_id'] ?? null;
        $orderId = $this->order->liqpay_order_id ?? null;

        if ($liqpayOrderId && $liqpayOrderId === $orderId) {
            return $liqpayOrderId;
        }

        return $liqpayOrderId ?: $orderId;
    }


    public function request($action = 'status', $orderId = 0)
    {
        //$orderId = (!empty($orderId)) ? $orderId : $this->order->liqpay_order_id;
        $orderId = (!empty($orderId)) ? $orderId : $this->getLiqpayOrderId();
        //Включить стоимость доставки, если она не оплачивается отдельно
        $amount = (!$this->order->is_delivery_paid_separately) ?
            $this->order->price_delivery + $this->order->cost :
            $this->order->cost;

        return $this->liqPay->request($orderId, $amount, $action);
    }
}
