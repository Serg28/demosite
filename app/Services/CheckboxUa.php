<?php
//https://wiki.checkbox.ua/uk/api/specification
//https://api.checkbox.in.ua/api/docs#/
//https://api.checkbox.in.ua/api/redoc


namespace App\Services;

use App\Models\LegalEntitiesRecipient;
use App\Models\OrderReceipt;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckboxUa
{
    private string $url;

    private string $login;

    private string $password;

    private string $licenseKey;

    private string $token;

    private string $methodShifts = '/api/v1/shifts';

    private string $type; //'prepayment' - предоплата, 'main_payment' - полный платеж;

    private ?LegalEntitiesRecipient $recipient;

    public function __construct(LegalEntitiesRecipient $recipient = null, $type = 'main_payment')
    {
        $this->recipient = $recipient ?? LegalEntitiesRecipient::default()->first();
        $this->url = ($this->recipient->checkbox_domain) ?? config('services.checkbox_ua.domain');
        $this->login = ($this->recipient->checkbox_login) ?? config('services.checkbox_ua.login');
        $this->password = ($this->recipient->checkbox_password) ?? config('services.checkbox_ua.password');
        $this->licenseKey = ($this->recipient->checkbox_license_key) ?? config('services.checkbox_ua.licenseKey');

        $this->type = $type;
        $this->token = $this->generateToken();
    }

    private function getSessionTokenName(): string
    {
        return 'checkbox_token' . md5($this->login);
    }

    public function getShift($id)
    {
        $queryUrl = $this->url . $this->methodShifts . '/' . $id;

        $response = Http::withToken($this->token)
            ->withHeaders(['X-License-Key' => $this->licenseKey])
            //->post($queryUrl);
            ->get($queryUrl);

        return $response->json();
    }

    public function closeShift()
    {
        $queryUrl = $this->url . '/api/v1/shifts/close';
        return Http::withToken($this->token)->post($queryUrl)->json();
    }

    /**
     * Возвращает токен для текущего кассира.
     * Если в базе данных отсутствует сохраненный токен, пытается получить его из сессии.
     * Если в сессии токен отсутствует, генерирует новый.
     * Сохраняет его в базе данных или в сессии, в зависимости от переданных параметров, и возвращает его.
     *
     * @return string Токен для текущего кассира.
     */
    public function generateToken(): string
    {
        // Если у получателя есть токен, используем его
        if ($this->recipient && !empty($this->recipient->checkbox_token)) {
            return $this->recipient->checkbox_token;
        }

        // Генерируем новый токен
        $token = $this->getAuthToken();

        // Если получатель существует и токен был успешно обновлен, обновляем его токен
        if ($this->recipient && $token !== null) {
            $this->recipient->update(['checkbox_token' => $token]);
        }

        return $token ?? '';
    }


    //Оригинальный метод
    //Создание чека и возврат результата
    /*public function createReceipt($order)
    {
        $data = $this->formDataForSending($order);
        Log::info('CreateReceipt: ' . print_r($this->testCreateReceipt($order), 1));
        $address = '/api/v1/receipts/sell';
        $this->checkShift();

        return $this->send('post', $address, $data);
    }*/

    //Создание чека и возврат результата. Добавлено расширенное логирование
    public function createReceipt($order)
    {
        $data = $this->formDataForSending($order);
        $address = '/api/v1/receipts/sell';
        $this->checkShift();

        $response = $this->send('post', $address, $data);
        Log::info('Checkbox, createReceipt. Response: order ' . $order->id . ': token - '. $this->token .', ' . print_r(
                $response,
                1
            ) . ', recipient: ' . print_r(($order->recipient->toArray() ?? []), 1) .', data: '.print_r($data, 1));

        return $response;
    }

    //Создание чека + запись в БД + отправка чека не email
    public function createAndSendReceipt($order): array
    {
        $response = $this->createReceipt($order);

        if (array_key_exists('message', $response)) {
            return [
                'success' => false,
                'title' => __cms('Ошибка'),
                'message' => __cms('Ошибка фискализации, ответ от Checkbox: '). $response['message'],
                'receipt' => null
            ];
        }

        $receipt = OrderReceipt::create([
            'uuid' => $response['id'],
            'order_id' => $order->id,
            'type' => $this->type
        ]);
        if ($order->email) {
            try {
                $this->sendReceiptToEmail($receipt, $order);
                Log::info('Checkbox success. Send Email to ' . $order->email . ' for order ' . $order->id);
            } catch (\Exception $e) {
                Log::error('Checkbox success. But failed to send email for order ' . $order->id . ': ' . $e->getMessage());
            }
        }
        return [
            'success' => true,
            'title' => __cms('Успех'),
            'message' => __cms('Фискализация прошла успешно'),
            'receipt' => $receipt
        ];
    }

    public function testCreateReceipt($order): array
    {
        $data = $this->formDataForSending($order);
        $address = '/api/v1/receipts/sell';
        $shift = $this->checkShift();

        return [
            'url' => $this->url,
            'login' => $this->login,
            'password' => $this->password,
            'licenseKey' => $this->licenseKey,
            'type' => $this->type,
            'token' => $this->token,
            'shift' => $shift,
            'address' => $address,
            'data' => $data
        ];
        //return $this->send('post', $address, $data);
    }

    public function getReceipt($id)
    {
        $address = $this->url . '/api/v1/receipts' . '/' . $id;

        return $this->send('get', $address);
    }

    public function getStatusReceipt($id)
    {
        $address = /*$this->url .*/
            '/api/v1/receipts' . '/' . $id;
        $response = $this->send('get', $address);
        return $response['transaction']['status'];
    }

    public function sendReceiptToEmail($receipt, $order)
    {
        $address = '/api/v1/receipts/' . $receipt->uuid . '/email';

        return $this->send('post', $address, [$order->email]);
    }


    //Возвращает статус текущей смены текущего кассира. Если смена закрыта, то null, иначе в status будет статус
    public function getCashierShift()
    {
        $address = '/api/v1/cashier/shift';

        return $this->send('get', $address);
    }

    private function getAuthToken()
    {
        $method = '/api/v1/cashier/signin';
        $data = [
            'login' => $this->login,
            'password' => $this->password,
        ];

        $queryUrl = $this->url . '' . $method;

        try {
            $user = Http::post($queryUrl, $data);
            if ($user->status() == 200) {
                return $user->json('access_token');
            }

            return abort(403, 'Something wrong');
        } catch (Exception $e) {
            //            until we use an exception
        }
    }

    //Открытие смены
    public function openShift()
    {
        $queryUrl = $this->url . '' . $this->methodShifts;

        $response = Http::withToken($this->token)
            ->withHeaders(['X-License-Key' => $this->licenseKey])
            ->post($queryUrl);

        return $response->json();
    }

    //Информация о статусе текущей смены
    public function getShifts()
    {
        $queryUrl = $this->url . $this->methodShifts;
        $response = Http::withToken($this->token)
            ->withHeaders(['X-License-Key' => $this->licenseKey])
            ->get($queryUrl);

        return $response->json();
    }

    /*private function checkShift(): void
    {
        $allShifts = $this->getShifts()['results'];
        $lastShift = end($allShifts);

        if ($lastShift['status'] == 'CLOSED') {
            $this->openShift();
        }
    }*/

    public function checkShift(): void
    {
        $shifts = $this->getShifts();

        if (isset($shifts['results']) && is_array($shifts['results']) && !empty($shifts['results'])) {
            $allShifts = $shifts['results'];
            $lastShift = end($allShifts);

            if ($lastShift['status'] == 'CLOSED') {
                $this->openShift();
            }
        } else {
            // Обработка случая, когда 'results' отсутствует, или это пустой массив
        }
    }

    //Обычный заказ с полным составом/Послеоплата -
    // Послеоплата пропорционально распределяется по товарам как скидка (по процентной доле товара в заказе)
    public function regularCheckoutStrategy($order)
    {
        $products = [];
        $prepaymentDiscount = $order->getPrepaymentAmount(); // Сумма предоплаты
        //$totalOrderAmount = $this->calculateTotalOrderAmount1($order); // Общая сумма заказа
        $totalOrderAmount = $order->cost;
        $totalPrepaymentDiscount = 0; // Итоговая сумма скидки предоплаты

        foreach ($order->products as $key => $orderProduct) {
            $productData = $orderProduct->product;
            $product = [];
            $product['code'] = $productData->code;
            $product['name'] = e($productData->t('title'));
            $products[$key]['quantity'] = $orderProduct->count * 1000;
            $product['price'] = ($orderProduct->base_price) * 100;
            $products[$key]['good'] = $product;

            // Распределение скидок
            $productDiscount = $this->calculateProductDiscount($orderProduct, $prepaymentDiscount, $totalOrderAmount);

            $totalPrepaymentDiscount += $productDiscount;

            if ($productDiscount > 0) {
                $discountsArr = [
                    'type' => 'DISCOUNT',
                    'mode' => 'VALUE',
                    'value' => $productDiscount,
                    'name' => __t('Скидка'),
                ];
                $products[$key]['discounts'] = [$discountsArr];
            }
        }

        $name = $order->manager ? $order->manager->last_name . ' ' . $order->manager->first_name : '';

        $price_delivery = 0;
        $discounts = [];
        if (!$order->is_delivery_paid_separately && $order->price_delivery) {
            $price_delivery = $order->price_delivery;
            $discounts[] = [
                'type' => 'EXTRA_CHARGE',
                'mode' => 'VALUE',
                'value' => $price_delivery * 100,
                'name' => __t('Доставка') . ' ' . $order->delivery->t('title'),
            ];
        }

        return array_filter([
            'cashier_name' => $name,
            'departament' => config('app.name'),
            'body' => [],
            'goods' => $products,
            'payments' => [
                [
                    'type' => $order->payMethod->getPaymentType(),
                    'value' => (($order->cost_without_sale + $price_delivery) * 100) - $totalPrepaymentDiscount,
                    'label' => !empty($prepaymentDiscount) ? __t('Післяплата') : $order->payMethod->getPaymentLabel(),
                ],
            ],
            'discounts' => $discounts,
            'rounding' => false,
            'footer' => __t('Фискальный чек'),
        ]);
    }

    //Возвращаем итоговую скидку на товар: обычная + распределенная предоплата
    /*public function calculateProductDiscount($orderProduct, $prepaymentDiscount, $totalOrderAmount): float|int
    {
        $productDiscount = $orderProduct->discount_amount * 100;

        if(!empty($prepaymentDiscount)) {
            // Распределение скидки на основе доли товара в общей сумме заказа
            $productPercentage = ($orderProduct->base_price / $totalOrderAmount) * 100;

            //$prepaymentDiscountPerProduct = ($prepaymentDiscount / 100) * $productPercentage;
            $prepaymentDiscountPerProduct = (int) round(($prepaymentDiscount / 100) * $productPercentage);

            $prepaymentDiscountPerProduct = (int)round($prepaymentDiscountPerProduct * 100) - $productDiscount;

            $productDiscount += $prepaymentDiscountPerProduct;
        }

        return $productDiscount;
    }*/

    public function calculateProductDiscount($orderProduct, $prepaymentDiscount, $totalOrderAmount): float|int
    {
        $productDiscount = $orderProduct->discount_amount * 100;

        if(!empty($prepaymentDiscount)) {
            // Распределение скидки на основе доли товара в общей сумме заказа
            $productPercentage = ($orderProduct->price * $orderProduct->count / $totalOrderAmount) * 100;

            //$prepaymentDiscountPerProduct = ($prepaymentDiscount / 100) * $productPercentage;
            $prepaymentDiscountPerProduct = round(($prepaymentDiscount / 100) * $productPercentage, 2);

            $prepaymentDiscountPerProduct = round($prepaymentDiscountPerProduct * 100, 2);

            $productDiscount = (int)($prepaymentDiscountPerProduct) + $productDiscount;
        }

        return $productDiscount;
    }

    //---

    //Платеж только для предоплаты
    private function prepaymentCheckoutStrategy($order)
    {
        $products = [];

        $product['code'] = 'PREPAY';
        $product['name'] = __t('Транспортные услуги');
        //$product['price'] = $order->prepayment_amount * 100; // сумма предоплаты
        $product['price'] = $order->getPrepaymentAmount() * 100; // сумма предоплаты
        $products[0]['good'] = $product;
        $products[0]['quantity'] = 1 * 1000;

        $name = '';
        if ($order->manager) {
            $name = $order->manager->last_name . ' ' . $order->manager->first_name;
        }

        return array_filter([
            'cashier_name' => $name,
            'departament' => config('app.name'),
            'body' => [],
            'goods' => $products,
            'payments' => [
                [
                    'type' => 'CASHLESS',
                    //"<"CASH"/"CASHLESS" (ГОТІВКА/БЕЗГОТІВКОВИЙ РОЗРАХУНОК (картка, сертифікати, бонуси тощо))>"
                    //'value' => $order->prepayment_amount * 100,
                    'value' => $order->getPrepaymentAmount() * 100,
                    'label' => __t('Карточка')
                ],
            ],
            'discounts' => [],
            'rounding' => false,
            'footer' => __t('Фискальный чек'),
        ]);
    }

    private function formDataForSending($order)
    {
        //return (!empty($order->prepayment_amount)
        return (!empty($order->getPrepaymentAmount())
            //&& in_array($order->PayMethod->checkout->slug, ['liqpaycod']))
            && $this->type === 'prepayment')
            ? $this->prepaymentCheckoutStrategy($order)
            : $this->regularCheckoutStrategy($order);
    }

    private function send($method, $address, $data = 0)
    {
        $urlSend = $this->url . '' . $address;
        if ($method == 'post') {
            $response = Http::withToken($this->token)->post($urlSend, $data);
        }
        if ($method == 'get') {
            $response = Http::withToken($this->token)->get($urlSend);
        }

        return $response->json();
    }
}
