<?php

namespace App\Services\Checkouts\LiqPay;

use App\Interfaces\Checkout;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LiqPayCOD extends LiqPay
{
    private $order;

    private $liqPay;

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->order = $order;
        $this->liqPay = new LiqPayService();
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
            $orderId = $this->order->id.'_'.time();
            $this->order->update([
                'prepayment_amount' => $amount,
                'liqpay_order_id' => $orderId,
            ]);
        }
        //--

        $currency = 'UAH';
        $action = ($action) ? $action : 'hold'; //Холдирование
        //$description = __t('Оплата транспортных услуг на сайте').' '.env('APP_NAME');
        $description = $this->getPrepaymentSum() ?
            __t('Оплата транспортных услуг на сайте').' '.env('APP_NAME') :
            __t('Оплата замовлення № ') . $this->order->id;;

        $resultUrl = route('payment.result', $this->order);
        $serverUrl = route('payment.confirm', $this->order);

        return $this->liqPay->pay($action, $amount, $currency, $description, $orderId, $resultUrl, $serverUrl);
    }

    public function confirm(): void
    {
        $data = request()->post('data');
        $liqSignature = request()->post('signature');
        //$localSignature = base64_encode(sha1($this->privateKey . $data . $this->privateKey, 1));
        $localSignature = $this->liqPay->getLocaleSignature($data);

        $prepayment_id = null;

        if ($localSignature === $liqSignature) {
            $data = json_decode(base64_decode($data), true);
            switch ($data['status']) {
                case 'success':
                    $status_pay = 1;
                    $fields['complect_status_id'] = 3;
                    $prepayment = $this->createOrderPayment($data['amount'], 'prepayment');
                    $prepayment_id = $prepayment->id;
                    break;
                case 'hold_wait':
                    $status_pay = 2;
                    break;
                default:
                    $status_pay = 0;
                    break;
            }
            $fields = [
                'is_online_payed' => $status_pay,
                'liqpay_info' => $data,
                'prepayment_id' => $prepayment_id
            ];
            $this->order->update($fields);

            return;
        }
        Log::error('Order Pay not confirmed.', request()->all());
    }

    public function request($action = 'status', $orderId = 0)
    {
        //$orderId = (! empty($orderId)) ? $orderId : $this->order->liqpay_order_id;
        $orderId = (!empty($orderId)) ? $orderId : $this->getLiqpayOrderId();
        $amount = $this->order->getPrepaymentAmount();

        return $this->liqPay->request($orderId, $amount, $action);
    }
}
