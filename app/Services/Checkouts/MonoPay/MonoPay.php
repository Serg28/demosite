<?php

namespace App\Services\Checkouts\MonoPay;

use App\Enums\PaymentStatusEnum;
use App\Interfaces\Checkout;
use App\Models\Order;
use App\Services\PaymentInvoice as PaymentInvoiceService;
use GuzzleHttp\Exception\GuzzleException;
use MonoPay\Webhook;

//https://github.com/plakidan/monobank-pay?tab=readme-ov-file
//Тест карта 4242 4242 4242 4242

class MonoPay implements Checkout
{

    private Order $order;
    private MonoPayService $monoPay;
    private string $redirectUrl;
    private string $webHookUrl;
    private PaymentInvoiceService $paymentInvoiceService;

    private string $publicKey;
    private int|float $validity;

    /**
     * Конструктор класса MonoPay.
     *
     * @param Order $order Заказ, для которого осуществляется оплата.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->monoPay = new MonoPayService();
        $this->paymentInvoiceService = new PaymentInvoiceService($this->order);

        $this->publicKey = $this->monoPay->getClient()->getPublicKey();

        //Адреса для повернення (GET) - на цю адресу буде переадресовано користувача після завершення оплати (у разі успіху або помилки)
        $this->redirectUrl = route('payment.result', $this->order);

        //Адреса для CallBack (POST) – на цю адресу буде надіслано дані про стан платежу при кожній зміні статусу. Зміст тіла запиту ідентичний відповіді запиту “перевірки статусу рахунку”
        $this->webHookUrl = route('payment.confirm', $this->order);

        //Строк дії інвойса в секундах, за замовчуванням рахунок перестає бути дійсним через 24 години
        $this->validity = 3600 * 24 * 7;
    }

    /**
     * Рассчитывает сумму предоплаты для данного метода оплаты.
     *
     * @return int|float|null Сумма предоплаты или null.
     */
    public function getPrepaymentSum(): int|float|null
    {
        if ($this->order->payMethod->isPrepaymentActive()) {
            $amount = $this->order->getPriceForDocumentsAttribute();
            $prepayment_percent = $this->order->payMethod->prepayment_percent;

            // Рассчитываем сумму предоплаты на основе процента
            $prepayment_amount = round(($amount * $prepayment_percent) / 100);

            // Проверяем, не меньше ли сумма предоплаты минимальной суммы предоплаты
            $min_prepayment_amount = $this->order->payMethod->min_prepayment_amount;
            return max($prepayment_amount, $min_prepayment_amount);
        }

        return 0;
    }

    /**
     * Возвращает сумму к оплате.
     *
     * @return int|float|null Сумма к оплате.
     */
    public function getAmountSum(): int|float|null
    {
        //Если активирована предоплата, возвращаем ее. Иначе - полную сумму заказа
        //Сумму умножаем на 100 по требованиям Monopay
        if ($this->order->payMethod->isPrepaymentActive()) {
            return $this->getPrepaymentSum() * 100;
        }
        return $this->order->getPriceForDocumentsAttribute() * 100;
    }

    /**
     * Инициализация платежа.
     *
     * @param string $action Тип операции ('debit' или 'hold').
     * Для значення hold термін складає 9 днів. Якщо через 9 днів холд не буде фіналізовано — він скасовується
     * @return string URL для перенаправления.
     */
    public function init(string $action = 'debit'): string
    {
        $amount = $this->getAmountSum();

        if (empty($amount)) {
            return redirect()->route('checkout.complete');
        }

        //створення платежу
        $destination = $this->getPrepaymentSum() ?
            __t('Оплата транспортных услуг на сайте') . ' ' . config('app.name') :
            __t('Оплата заказа № ') . $this->order->id;

        //Используем уникальный номер заказа для MonoPay
        $invoice = $this->paymentInvoiceService->getOrCreatePaymentInvoice($this->order->payMethod->checkout->slug ?? 'monopay');

        $result = $this->monoPay->getPayment()->create(
            $amount,
            [
                //деталі оплати
                'merchantPaymInfo' => [
                    'reference' => $invoice->order_reference_id,
                    //номер чека, замовлення, тощо; визначається мерчантом (вами)
                    'destination' => $destination,
                    //призначення платежу
                    'basketOrder' => $this->formatProductsForInvoice(),
                ],
                'redirectUrl' => $this->redirectUrl,
                'webHookUrl' => $this->webHookUrl,
                'validity' => $this->validity,
                'paymentType' => $action,
            ]
        );

        //Ответ при успехе: "{"invoiceId":"240828EkRpQgLG9hU914","pageUrl":"https:\/\/pay.mbnk.biz\/240828EkRpQgLG9hU914"}"
        if (isset($result['invoiceId'], $result['pageUrl'])) {
            $invoice->update(['invoice_id' => $result['invoiceId']]);
            return redirect($result['pageUrl'])->send();
        }

        \Log::warning('MonoPay order ' . $this->order->id . ' init: pageUrl not found. Response: ' . json_encode($result));
        return redirect()->route('checkout.complete');
    }

    /**
     * Проверка статуса платежа.
     *
     * @throws GuzzleException
     * @return bool Статус платежа.
     */
    public function status(): bool
    {
        $invoice = $this->paymentInvoiceService->getPaymentInvoiceByMethod($this->order->payMethod->slug ?? 'monopay');

        if ($invoice) {
            $response = $this->monoPay->getPayment()->info($invoice->invoice_id);
            $statusInfo = json_encode($response, true);

            $this->paymentInvoiceService->updatePaymentInfo($invoice, (array)$response);

            return $statusInfo;
        }

        return false;
    }

    /**
     * Подтверждение платежа.
     *
     * @throws GuzzleException
     * @return void
     */
    public function confirm(): void
    {
        try {
            $sign = request()->header('X-Sign');

            $monoWebhook = new Webhook($this->monoPay->getClient(), $this->publicKey, $sign);

            $response = file_get_contents('php://input');

            \Log::info('MonoPay confirm for order ' . $this->order->id . ' body: ' . json_encode($response));

            //валідуємо дані
            if ($monoWebhook->verify($response)) {
                //echo 'Ці дані прислав монобанк, можна обробляти';

                $response = is_string($response) ? json_decode($response, true) : $response;

                if(isset($response['status'])) {
                    switch ($response['status']) {
                        case 'success':
                        case 'ok':
                            $status_pay = PaymentStatusEnum::Paid(); //$status_pay = 1
                            $this->createOrderPayment($response['amount']);
                            break;
                        case 'hold':
                            $status_pay = PaymentStatusEnum::Blocked(); //$status_pay = 2;
                            break;
                        case 'processing':
                            $status_pay = PaymentStatusEnum::Awaiting(); //$status_pay = 3;
                            break;
                        default:
                            $status_pay = PaymentStatusEnum::Unpaid(); //$status_pay = 0;
                            break;
                    }

                    //Устанавливаем статус оплаты заказа
                    $this->order->update(['is_online_payed' => $status_pay]);

                    //Обновляем информацию из ответа в инвойсе
                    if(isset($response['invoiceId'])) {
                        $invoice = $this->paymentInvoiceService->getInvoiceById($response['invoiceId']);
                        $this->paymentInvoiceService->updatePaymentInfo($invoice, (array)$response);
                    }
                }

            } /*else {
                //echo 'Дані прислав шахрай, ігноруємо';
            }*/
        } catch (\Exception $exception) {
            \Log::error('MonoPay confirm for order ' . $this->order->id . ' error: ' . $exception->getMessage());
        }
    }

    /**
     * Создает запись об оплате заказа.
     *
     * @param float $price Сумма оплаты.
     * @param string $type Тип оплаты.
     * @param int $is_payed Флаг оплаты.
     */
    private function createOrderPayment($price, $type = 'main_payment', $is_payed = 1)
    {
        return $this->order->payments()->create([
            'order_id' => $this->order->id,
            'legal_entities_recipient_id' => $this->order->legal_entities_recipient_id,
            'type' => $type,
            'price' => (float)$price/100, //преобразуем в гривны из копеек
            'is_payed' => $is_payed
        ]);
    }

    /**
     * Подготавливает название продукта.
     *
     * @param string $name Название продукта.
     * @return array|string Обработанное название продукта.
     */
    private function prepareProductName($name): array|string
    {
        return str_replace(array("'", '"', '&#39;', '&'), '', htmlspecialchars_decode($name));
    }

    /**
     * Форматирует продукты для инвойса.
     *
     * @return array Отформатированный массив продуктов.
     */
    private function formatProductsForInvoice(): array
    {
        return $this->order->products->map(function ($orderProduct) {
            return [
                'name' => $this->prepareProductName($orderProduct->title), // Название товара
                'qty' => (int)$orderProduct->count, // Количество
                'sum' => (int)$orderProduct->price * 100, // Сумма в минимальных единицах валюты
                'icon' => $orderProduct->product->getPicture() ?? '', // Ссылка на изображение товара
                'unit' => $orderProduct->product->unit ?? 'шт.', // Название единицы измерения товара
            ];
        })->toArray();
    }

}