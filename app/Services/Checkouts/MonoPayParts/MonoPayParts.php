<?php
//https://u2-demo-ext.mono.st4g3.com/docs/index.html#section/Avtorizaciya-(pidpis-zapitiv-vidpovidej)/Production

namespace App\Services\Checkouts\MonoPayParts;

use App\Interfaces\Checkout;
use App\Models\Order;
use App\Services\Checkouts\MonoPayParts\src\Api\Order as MonoOrder;
use App\Services\Checkouts\MonoPayParts\src\Config;
use App\Services\Checkouts\MonoPayParts\src\Invoice;
use App\Services\Checkouts\MonoPayParts\src\Mode;
use App\Services\Checkouts\MonoPayParts\src\Purchase;
use Illuminate\Support\Facades\Log;

class MonoPayParts implements Checkout
{
    private Order $order;

    private Config $config;

    private $resultUrl;

    private $serverUrl;

    private $mode;

    private Invoice $invoice;

    private Purchase $purchase;

    private MonoOrder $monoorder;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->resultUrl = route('payment.result', $this->order);
        $this->serverUrl = route('payment.confirm', $this->order);
        $this->mode = new Mode();
        $this->config = new Config($this->mode, $this->serverUrl);
        $this->monoorder = new MonoOrder($this->config);
    }

    public function init(): string
    {
        //Используем уникальный номер заказа для МОНО
        $orderId = $this->order->mono_store_order_id;
        if (empty($orderId)) {
            $orderId = $this->order->id . '_' . time();
            $this->order->update([
                'mono_store_order_id' => $orderId,
            ]);
        }
        //--

        $this->invoice = new Invoice(
            $orderId,
            $this->order->phone,
            $this->order->id,
            [(int)$this->order->payparts_count]
        );
        $this->purchase = new Purchase($this->config, $this->invoice);

        foreach ($this->order->cartOrderProducts as $product) {
            $this->purchase->addProduct($product->t('title'), $product->pivot->count, $product->price);
        }

        $response = $this->monoorder->create($this->purchase);
        $response = $this->responseToArray($response);


        //Если была ошибка
        if (isset($response['errors'])) {
            $this->order->update([
                'note_for_manager' => __cms("Ошибка оформления Моно - оплата частями. Ошибки: ") . implode(
                    '; ',
                    $response['errors']
                ),
            ]);
        } else {
            //Записываем системный ответ
            $this->updateInfo($response);
        }
        return redirect($this->resultUrl)->send();
    }

    public function status(): bool
    {
        $state = $this->monoorder->state($this->order->mono_order_id);
        $response = $this->responseToArray($state);
        $this->updateInfo($response); //Записываем системный ответ

        return json_encode($response, true);
    }

    public function info()
    {
        $state = $this->monoorder->state($this->order->mono_order_id);
        return $this->responseToArray($state);
        //$this->updateInfo($response); //Записываем системный ответ

        //return json_encode($response, true);
    }

    /**
     * Bank response processing
     */
    public function confirm(): void
    {
        /*
        Статус	    Суб-статус	                        Пояснення
        SUCCESS	    ACTIVE	                            заявка успішна, товар передано клієнтові, гроші надіслано магазину. Фінальний статус за заявкою
        SUCCESS	    DONE	                            заявка успішна, товар передано клієнтові, гроші надіслано магазину, ПЧ погашено клієнтом.
        SUCCESS	    RETURNED	                        магазином прийнято повернення товару, гроші перераховано клієнтові
        IN_PROCESS	WAITING_FOR_CLIENT	                очікування підтвердження від клієнта кредитного договору у застосунку монобанк
        IN_PROCESS	WAITING_FOR_STORE_CONFIRM	        кредитна угода ПЧ підтверджена клієнтом. Важливо! Ключовий статус після отримання якого необхідно передати товар клієнтові
        FAIL	    CLIENT_NOT_FOUND	                Клієнта не знайдено. Варіанти: не є клієнтом монобанку; зазначено не фінансовий номер
        FAIL	    EXCEEDED_SUM_LIMIT	                Клієнт перевищив допустимий ліміт на ПЧ. Ліміт можна подивитися у застосунку монобанк у меню Розстрочка.
        FAIL	    EXISTS_OTHER_OPEN_ORDER	            У клієнта є інша відкрита заявка на ПЧ. Рішення: скасувати відкриту заявку у застосунку клієнтом чи магазином методом reject; почекати 15 хв, заявка перейде у статус CLIENT_PUSH_TIMEOUT
        FAIL	    FAIL	                            Внутрішня помилка на боці Банку. Рекомендуємо повторити подання заявки через 5 хв.
        FAIL	    NOT_ENOUGH_MONEY_FOR_INIT_DEBIT	    Недостатньо коштів для першого списання. Рішення: поповнити картку монобанку на суму першого платежу
        FAIL	    REJECTED_BY_CLIENT	                Клієнт відмовився від здійснення купівлі
        FAIL	    RESTRICTED_BY_RISKS	                Потрібно звернутися до банку для отримання причини відмови у ПЧ
        FAIL	    CLIENT_PUSH_TIMEOUT	                Клієнт не ухвалив рішення щодо кредитного договору ПЧ у застосунку монобанку. Кредитний договір активний 15 хв. Рішення: зв'язатися з клієнтом; повторити заявку
        FAIL	    REJECTED_BY_STORE	                Магазин відмовився від продажу

        FAIL	PAY_PARTS_ARE_NOT_ACCEPTABLE            із цією кількістю платежів клієнт не може оформити розстрочку
        FAIL	CLIENT_CONFIRM_TIME_EXPIRED             минув час підтвердження заявки клієнтом
        IN_PROCESS	ADDED                               заявку додано на опрацювання
        */

        //$data = request()->post();
        //$response = json_decode($data, true);
        //$state = $this->monoorder->state($response['order_id']); //{"order_id":"a0eebc99-9c0b-4ef8-bb6d-000000000001","state":"SUCCESS","order_sub_state":"ACTIVE","message":null}

        if ($response = $this->info()) {
            $response = $this->responseToArray($response);

            $this->updateInfo($response);
            Log::info('Order MonoPay confirm: '. print_r($response, 1));
            return;
        }
    }

    /**
     * Checking if the order is fully paid
     * Отримання ознаки того, що заявка повністю оплачена
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function isPaid()
    {
        return $this->monoorder->isPaid($this->order->mono_order_id);
    }

    /**
     * Confirmation that the goods have been delivered to the client
     * Підтвердження видачі товару клієнтові
     *
     * @return array
     */
    public function confirmation()
    {
        $response = $this->monoorder->confirm($this->order->mono_order_id);
        $response = $this->responseToArray($response);
        $this->confirm(); //На тестовом статус со стороны Банка не меняется. Потом проверим на боевом
        //$this->updateInfo($response); //вместо $this->confirm();
        return $response;
    }


    /**
     * Canceling the order (the product was not issued to the client)
     * Скасування заявки (Товар клієнтові не видано)
     *
     * @return array
     */
    public function reject()
    {
        $response = $this->monoorder->reject($this->order->mono_order_id);
        $response = $this->responseToArray($response);
        $this->confirm(); //На тестовом статус со стороны Банка не меняется. Потом проверим на боевом
        //$this->updateInfo($response); //вместо $this->confirm();
        return $response;
    }


    /**
     * Return of goods upon request (full or partial)
     * Повернення товару за заявкою (повне або часткове)
     *
     * @return array
     */
    public function return()
    {
        /*
         * й зверніть увагу, якщо будуть часткові повернення, то return order id має бути унікальним, інакше ми вважатиме новий запит дублем
         *
         */
        $return_money_to_card = true; // надо утонить
        //Включить стоимость доставки, если она не оплачивается отдельно
        $amount = (!$this->order->is_delivery_paid_separately) ? //надо уточнить
            $this->order->price_delivery + $this->order->cost :
            $this->order->cost;
        $response = $this->monoorder->return(
            $this->order->mono_order_id,
            $return_money_to_card,
            $this->order->mono_store_order_id,
            $amount
        );
        $response = $this->responseToArray($response);
        //mono_store_order_id
        $this->confirm();
        return $response;
    }

    public function responseToArray($body): array
    {
        return is_array($body) ? $body : json_decode($this->response($body), true);
    }

    public function response($body)
    {
        //$response = $body->getBody()->getContents();
        //return $response;
        return $body->getBody()->getContents();
    }

    public function updateInfo(array $response): void
    {
        if ($response) {
            $fields = [
                'mono_payparts_info' => $response,
                'mono_order_id' => @$response['order_id'],
            ];
            switch (@$response['order_sub_state']) {
                case 'ACTIVE':
                case 'DONE':
                    $status_pay = 1;
                    break;
                default:
                    $status_pay = 0;
                    break;
            }
            $fields['is_online_payed'] = $status_pay;
            $this->order->update($fields);
        }
    }
}
