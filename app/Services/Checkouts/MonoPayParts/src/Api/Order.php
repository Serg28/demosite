<?php

namespace App\Services\Checkouts\MonoPayParts\src\Api;

use App\Services\Checkouts\MonoPayParts\src\Api;
use App\Services\Checkouts\MonoPayParts\src\Purchase;
use Illuminate\Support\Facades\Validator;

class Order extends Api
{
    const API_URL_ORDER_CHECK_PAID = 'api/order/check/paid';

    const API_URL_ORDER_CONFIRM = 'api/order/confirm';

    const API_URL_ORDER_CREATE = 'api/order/create';

    const API_URL_ORDER_REJECT = 'api/order/reject';

    const API_URL_ORDER_RETURN = 'api/order/return';

    const API_URL_ORDER_STATE = 'api/order/state';

    public function isPaid(string $orderId)
    {
        return $this->send([
            'order_id' => $orderId,
        ], self::API_URL_ORDER_CHECK_PAID);
    }

    public function confirm(string $orderId)
    {
        return $this->send([
            'order_id' => $orderId,
        ], self::API_URL_ORDER_CONFIRM);
    }

    public function create(Purchase $purchase)
    {
        //---Default
        /*$validator = $this->validator()->validate(
            $purchase->getRequestArray(),
            $this->rule()->getOrderCreateRules(),
            $this->rule()->getOrderCreateMessages()
        );
        return $this->send($purchase->getRequestArray(), self::API_URL_ORDER_CREATE);
        */
        //----

        //---custom
        $validator = Validator::make($purchase->getRequestArray(),
            $this->rule()->getOrderCreateRules()
        );

        if (!$validator->fails()) {
            return $this->send($purchase->getRequestArray(), self::API_URL_ORDER_CREATE);
        }

        return ['errors'=>$validator->errors()->all()];
        //---

    }

    public function reject(string $orderId)
    {
        return $this->send([
            'order_id' => $orderId,
        ], self::API_URL_ORDER_REJECT);
    }

    public function return(string $orderId, float $returnMoneyToCard, string $storeReturnId, float $sum, float $nds = null)
    {
        $body = [
            'order_id' => $orderId,
            'return_money_to_card' => $returnMoneyToCard,
            'store_return_id' => $storeReturnId,
            'sum' => $sum,
        ];

        if ($nds !== null) {
            $body['additional_params']['nds'] = $nds;
        }

        return $this->send($body, self::API_URL_ORDER_RETURN);
    }

    public function state(string $orderId)
    {
        return $this->send([
            'order_id' => $orderId,
        ], self::API_URL_ORDER_STATE);
    }
}
