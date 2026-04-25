<?php

namespace App\Services\Checkouts\PrivatPayParts;

use App\Interfaces\Checkout;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * Класс для оплаты частями от ПриватБанк https://pbdocs.gitbook.io/api-oc/testdata
 */
class PrivatPayParts implements Checkout
{
    private Order $order;

    private PrivatPayPartsService $privatPayParts;

    private string $storeId;

    private string $password;

    private string $type;

    /**
     * PrivatPayParts constructor.
     * создаём идентификаторы магазина
     *
     * @param  Order  $order
     */

    //public function __construct($StoreId, $Password)
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->storeId = config('services.privat_pay_parts.store_id');
        $this->password = config('services.privat_pay_parts.password');
        $this->type = config('services.privat_pay_parts.type');
        $this->privatPayParts = new PrivatPayPartsService();
    }

    public function init($method = 'pay'): string
    {
        //Используем уникальный номер заказа для Privat
        $orderId = $this->order->privat_order_id;
        if (empty($orderId)) {
            $orderId = $this->order->id.'_'.time();
            $this->order->update([
                'privat_order_id' => $orderId,
            ]);
        }
        //--

        $resultUrl = route('payment.result', $this->order);
        $serverUrl = route('payment.confirm', $this->order);

        $productsOrder = $this->order->cartOrderProducts;
        $productsList = [];

        foreach ($productsOrder as $product) {
            $productsList[] = [
                'name' => $product->t('title'),
                'price' => $product->price,
                'count' => $product->pivot->count,
            ];
        }

        $options = [
            'ResponseUrl' => $serverUrl,                        //URL, на который Банк отправит результат сделки (НЕ ОБЯЗАТЕЛЬНО)
            'RedirectUrl' => $resultUrl,                        //URL, на который Банк сделает редирект клиента (НЕ ОБЯЗАТЕЛЬНО)
            'PartsCount' => (int)$this->order->payparts_count,  //Количество частей на которые делится сумма транзакции ( >1)
            'Prefix' => '',                                     //Параметр не обязательный если Prefix указан с пустотой или не указа вовсе префикс будет ORDER
            'OrderID' => $orderId,                              //Если OrderID задан с пустотой или не укан вовсе OrderID сгенерится автоматически
            'merchantType' => $this->type,                      //II - Мгновенная рассрочка; PP - Оплата частями; PB - Оплата частями. Деньги в периоде. IA - Мгновенная рассрочка. Акционная.
            'Currency' => '980',                                //Валюта по умолчанию 980 – Украинская гривна; Значения в соответствии с ISO
            'ProductsList' => $productsList,                    //Список продуктов, каждый продукт содержит поля: name - Наименование товара price - Цена за еденицу товара (Пример: 100.00) count - Количество товаров данного вида
            'recipientId' => '',                                 //Идентификатор получателя, по умолчанию берется основной получатель. Установка основного получателя происходит в профиле магазина.
        ];

        $this->privatPayParts->setOptions($options);
        $send = $this->privatPayParts->create($method); //hold //pay

        //Записываем системный ответ
        if ($send) {
            $this->order->update([
                'privat_payparts_info' => $send,
            ]);
        }

        return redirect()->to('https://payparts2.privatbank.ua/ipp/v2/payment?token='.@$send['token'])->send();
    }

    public function status(): bool
    {
        return $this->privatPayParts->getState($this->order->privat_order_id, false); //orderId, showRefund
    }

    public function confirm(): void
    {
        $data = request()->getContent();
        //Log::error('PrivatPay confirm for Order.', $data);
        //$data = request()->post();

        $result = $this->privatPayParts->checkCallBack($data);

        /*
        CREATED	- Платіж створено
        CANCELED -	Платіж скасовано (клієнтом)
        SUCCESS	- Платіж здійснено успішно
        FAIL -	Помилка під час створення платежу
        CLIENT_WAIT -	Очікування оплати клієнта
        OTP_WAITING -	Підтвердження клієнтом ОТП-пароля
        PP_CREATION -	створення контракту для платежу
        LOCKED -	Платіж підтверджений клієнтом і чекає на підтвердження магазином.
        */

        if ($result !== 'error') {
            switch ($result['paymentState']) {
                case 'SUCCESS': //Платіж здійснено успішно
                    $status_pay = 1;
                    $fields['complect_status_id'] = 3;
                    break;
                default:
                    $status_pay = 0;
                    break;
            }
            $fields = [
                'is_online_payed' => $status_pay,
                'privat_payparts_info' => $data,
            ];
            $this->order->update($fields);

            return;
        }
        Log::error('Order Pay not confirmed.', request()->all());
    }
}
