<?php

namespace App\Services\Checkouts\WayForPay;

use App\Enums\PaymentStatusEnum;
use App\Interfaces\Checkout;
use App\Models\Order;
use App\Services\PaymentInvoice as PaymentInvoiceService;
use Illuminate\Support\Facades\Http;
use Maksa988\WayForPay\Domain\Client;
use \Maksa988\WayForPay\Collection\ProductCollection;
use WayForPay\SDK\Domain\MerchantTypes;
use \WayForPay\SDK\Domain\Product;
use \Maksa988\WayForPay\WayForPay as WayForPayService;

class WayForPay implements Checkout
{
    private Order $order;

    private Client $client;

    private WayForPayService $wayForPay;

    private PaymentInvoiceService $paymentInvoiceService;

    private array $config;

    private bool $log;

    /**
     * Конструктор класса MonoPay.
     *
     * @param Order $order Заказ, для которого осуществляется оплата.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->client =  new Client($this->order->user->first_name?? $this->order->first_name, $this->order->user->last_name ?? $this->order->last_name, $this->order->user->email??$this->order->email, $this->order->user->phone??$this->order->phone);
        $this->wayForPay = new WayForPayService();

        $this->paymentInvoiceService = new PaymentInvoiceService($this->order);

        $lang = \App::getLocale();
        $lang = $lang === 'uk' ? 'UA' : $lang;

        $this->log = true;

        $this->config = [
            // Адрес возврата после успешного/неуспешного платежа
            'returnUrl' => route('payment.result', $this->order),
            // Адрес для получения от платежной системы результатов платежа
            'serviceUrl' => route('payment.confirm', $this->order),
            // Срок действия блокировки средств в секундах. Минимум 60 секунд
            'holdTime' => 1728000,
            'lang' => $lang,
            'currency' => 'UAH',
        ];
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
        if ($this->order->payMethod->isPrepaymentActive()) {
            return $this->getPrepaymentSum();
        }
        return $this->order->getPriceForDocumentsAttribute();
    }

    /**
     * Инициализация платежа.
     *
     * @return string URL для перенаправления.
     * @throws \Exception
     */
    public function init(): string
    {
        $amount = $this->getAmountSum();

        if (empty($amount)) {
            return redirect()->route('checkout.complete');
        }

        // Используем уникальный номер заказа для WayForPay
        // Сначала получим слаг платежной системы
        $paymentMethodSlug = $this->order->payMethod->checkout->slug ?? 'wayforpay';

        // Добавляем в БД запись с инвойсом для заказа
        $invoice = $this->paymentInvoiceService->getOrCreatePaymentInvoice($paymentMethodSlug);

        // Для создания платежа берем либо сгенерированный выше для WayForPay номер или номер заказа
        $orderReferenceId = $invoice->order_reference_id ?? $this->order->id;

        // Формируем список товаров для платежа
        $formattedProducts = $this->formatProductsForInvoice();

        // Формируем платеж
        $formData = $this->wayForPay->purchase(
            $orderReferenceId,
            $amount,
            $this->client,
            $formattedProducts,
            $this->config['currency'],
            null,
            $this->config['lang'],
            $this->order->id,
            $this->config['returnUrl'],
            $this->config['serviceUrl'],
            null,
            MerchantTypes::TRANSACTION_AUTO,
            MerchantTypes::TRANSACTION_SECURE_AUTO,
            $this->config['holdTime']
        )->getData();

        if ($formData) {
            $response = Http::asForm()->post('https://secure.wayforpay.com/pay?behavior=offline', $formData);
            $responseBody = json_decode($response->body(), true);

            if($this->log) {
                \Log::info('WayForPay order '.$this->order->id.' init payresult: ' . json_encode($responseBody));
            }

            if (!empty($responseBody['url'])) {
                $invoice->update(['invoice_id' => $orderReferenceId]); //TODO: проверить, есть ли ИД инвойса от WayForPay
                return redirect($responseBody['url'])->send();
            }

            if($this->log) {
                \Log::warning('WayForPay order ' . $this->order->id . ' init: payment URL not found. Response: ' . json_encode($responseBody));
            }
        }

        return redirect()->route('checkout.complete');
    }


    /**
     * Проверка статуса платежа.
     *
     * @return bool Статус платежа.
     */
    public function status(): bool
    {
        $invoice = $this->paymentInvoiceService->getPaymentInvoiceByMethod($this->order->payMethod->slug ?? 'wayforpay');

        if ($invoice) {

            $order = $this->wayForPay->check($this->order->id)->getOrder();

            $this->paymentInvoiceService->updatePaymentInfo($invoice, (array)$order);

            return $order->getStatus();
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public function confirm(): void
    {
        if($this->log) {
            \Log::info('WayForPay order ' . $this->order->id . ' confirm data: ' . \file_get_contents('php://input'));
        }

        $request = \json_decode(file_get_contents('php://input'), TRUE);

        $this->wayForPay->handleServiceUrl($request, function ($transaction, $success) use ($request) {
            if($transaction->getReason()->isOK()) {

                //Устанавливаем статус оплаты заказа
                $this->order->update(['is_online_payed' => PaymentStatusEnum::Paid()]);

                //Создаем запись о сумме платежа
                $this->createOrderPayment($request['amount']);

                //Обновляем информацию в инвойсе из ответа
                if(isset($request['orderReference'])) {
                    $invoice = $this->paymentInvoiceService->getInvoiceById($request['orderReference']);
                    $this->paymentInvoiceService->updatePaymentInfo($invoice, (array)$request);
                }

                return $success();
            }
            if($this->log) {
                \Log::info('WayForPay order '.$this->order->id.' payment error: ' . $transaction->getReason()->getMessage());
            }
            return "Error: ". $transaction->getReason()->getMessage();
        });
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
            'price' => (float)$price, //преобразуем в гривны из копеек
            'is_payed' => $is_payed
        ]);
    }

    /**
     * Подготавливает название продукта.
     *
     * @param string $name Название продукта.
     * @return array|string Обработанное название продукта.
     */
    private function prepareProductName(string $name): array|string
    {
        return str_replace(array("'", '"', '&#39;', '&'), '', htmlspecialchars_decode($name));
    }

    /**
     * Форматирует продукты для инвойса.
     *
     * @return ProductCollection Отформатированный массив продуктов.
     */
    private function formatProductsForInvoice(): ProductCollection
    {
        $products = $this->order->products->map(function ($orderProduct) {
            $title = $orderProduct->title ?? $orderProduct->product->t('title');
            $preparedName = $this->prepareProductName($title);
            $price = $orderProduct->price;
            $count = (int) $orderProduct->count;

            return new Product($preparedName, $price, $count);
        })->toArray();

        return new ProductCollection($products);
    }
}
