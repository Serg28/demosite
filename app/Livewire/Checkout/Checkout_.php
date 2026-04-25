<?php

namespace App\Livewire\Checkout;

use App\Enums\DeliveryMethodEnum;
use App\Events\OrderCreate;
use App\Models\City;
use App\Models\Delivery;
use App\Services\DeliveryTime;
use App\Services\UnfinishedBasketService;
use App\Services\UserService;
use Illuminate\Support\Arr;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Services\Checkout as CheckoutService;

class Checkout_ extends Component
{
    #[Locked]
    private CheckoutService $checkoutService;

    #[Locked]
    public string|null $user = null;

    #[Locked]
    public string|null $promocode = null;

    #[Locked]
    public $deliveries;

    #[Locked]
    public ?string $delivery_form;

    #[Locked]
    public $payments;

    #[Validate('required')]
    public string|null $first_name = null;

    #[Validate('required')]
    public string|null $last_name = null;

    #[Validate('required')]
    public string|null $phone = null;

    #[Validate('required')]
    #[Validate('email')]
    public string|null $email = null;

    public string|null $patronymic = null;

    public string|null $receiver = 'user';
    public string|null $receiver_first_name = null;
    public string|null $receiver_last_name = null;
    public string|null $receiver_patronymic_name = null;
    public string|null $receiver_phone = null;
    public string|null $receiver_email = null;
    public string|null $comment = null;
    public int $call_me = 1;
    public int $register_me = 1;
    public string|null $vin = null;
    public string|int|null $city_id = null;

    #[Validate('required')]
    public string|int|null $pay_method_id = null;

    #[Validate('required')]
    public string|int|null $delivery_id = 2;
    public string|int|null $delivery_type = 'np';
    public string|int|null $payparts_count = null;
    public string|int|null $np_warehouse_id = null;
    public string|int|null $delivery_pickup_point_id = null;
    public string|null $address = null;
    public string|int|null $ukrposhta_warehouse_id = null;
    public string|int|null $justin_warehouse_id = null;
    public string|int|null $meest_warehouse_id = null;

    public $cities = null;

    public $checkoutErrors;

    public function boot(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;

        $this->withValidator(function ($validator) {
            $this->checkoutErrors = collect($validator->errors());
            /*$validator->after(function ($validator) {
                if (str($this->title)->startsWith('"')) {
                    $validator->errors()->add('title', 'Titles cannot start with quotations');
                }
            });*/
        });
    }

    public function mount(): void
    {
        //Установка данных пользователя
        $this->loadUserProperty();

        //Установка метода доставки по-умолчанию
        $this->selectDelivery($this->delivery_id, $this->delivery_type);
    }

    //Перезагружаем компонент при вызове события cart-changed
    #[On('cart-changed')]
    public function loadCheckout(): bool
    {
        return true;
    }

    #[On('checkout-set-property')]
    public function updating($property, $value): void
    {
        //$this->saveOrderField($property, $value);

        $this->setProperty($property, $value);
        //$this->setDeliveryRules();

        $this->withValidator(function ($validator) {
            $this->checkoutErrors = collect($validator->errors());
            /*$validator->after(function ($validator) {
                if (str($this->title)->startsWith('"')) {
                    $validator->errors()->add('title', 'Titles cannot start with quotations');
                }
            });*/
        });

        $this->dispatch('cart-changed');
        $this->validate($this->getRules());

    }

    //Устанавливаем свойство
    //#[On('checkout-set-property')]
    public function setProperty($property, $value): void
    {
        $this->{$property} = $value;

        $this->saveOrderField($property, $value);

        $this->setDeliveryRules();

        if($property === 'city_id') {
            $this->resetDeliveryFields();
        }

        if ($property === 'delivery_id') {
            $this->resetDeliveryCityFields();
        }
    }

    //Устанавливаем несколько свойств
    #[On('checkout-set-properties')]
    public function setProperties(array $properties = []): void
    {

        foreach ($properties as $item) {
            if (isset($item['model'], $item['value'])) {
                $this->setProperty($item['model'], $item['value']);
            }
        }
        $this->dispatch('cart-changed');
        //$this->dispatch('checkout-errors', errors: $this->getErrorBag());
        $this->validate($this->getRules());

    }

    //Выбор метода оплаты при клике на чекбокс
    public function selectPayment($payment_id): void
    {
        $this->updating('pay_method_id', $payment_id);
    }

    //Выбор метода доставки при клике на чекбокс
    public function selectDelivery($delivery_id, $delivery_type): void
    {
        $this->delivery_id = $delivery_id;
        $this->delivery_type = $delivery_type;

        $this->setProperties([
                ['model' => 'delivery_id', 'value' => $delivery_id],
                ['model' => 'delivery_type', 'value' => $delivery_type]
            ]
        );
    }

    //Процесс создания заказа
    public function submit() {

        $this->validate($this->getRules());

        $data = $this->except(['g-recaptcha-response', 'g_recaptcha_response', '_token']);

        $order = $this->checkoutService->createOrder($data);

        if ($order->payMethod->checkout) {
            return redirect()->to($order->urlPayment());
        }

        return redirect()->route('checkout.complete');
    }

    public function render()
    {
        $cartValues = $this->checkoutService->getCartValues();

        $checkout = $cartValues->toArray();
        $payment = $this->checkoutService->paymentForms($this->pay_method_id, $this->payparts_count);

        $checkout = array_merge([
            'payment_form' => $payment['form'] ?? '', //??
            'payment_cart' => $payment['cart'] ?? '' //??
        ], $checkout);

        $this->deliveries = $this->checkoutService->getDeliveries($this->city_id, true); //второй параметр - игнорировать ли выбор города
        $this->delivery_form = $this->checkoutService->getDeliveryPointers($this->city_id, $this->delivery_id, $this->delivery_type, $this->checkoutErrors);
        $this->payments = $this->checkoutService->getPayments($this->delivery_id);

        return view('livewire.checkout.checkout')->with($checkout);
    }

    //Загрузка данных пользователя
    private function loadUserProperty(): void
    {
        $user = $this->checkoutService->getUserData();
        foreach($user as $model => $value) {
            $this->{$model} = $value;
        }
    }

    //Сброс города + полей, связанных с доставкой и получателем
    private function resetDeliveryCityFields(): void
    {
        $this->city_id = null;
        $this->resetDeliveryFields();
    }

    //Сброс полей, связанных с доставкой и получателем
    private function resetDeliveryFields(): void
    {
        $this->reset(
            [
                'np_warehouse_id',
                'delivery_pickup_point_id',
                'address',
                'ukrposhta_warehouse_id',
                'justin_warehouse_id',
                'meest_warehouse_id',
                'receiver',
                'receiver_first_name',
                'receiver_last_name',
                'receiver_patronymic_name',
                'receiver_phone',
                'receiver_email',
                'pay_method_id'
            ]
        );
    }

    //Сохраняем поле в сессию
    private function saveOrderField($property, $value): void
    {
        $orderForm = session()->get('orderForm', []);
        $orderForm = Arr::except($orderForm, ['g_recaptcha_response', 'recaptcha']);
        $orderForm[$property] = $value;
        session()->put('orderForm', $orderForm);
    }

    //Установка правил валидации, зависящих от выбранного метода доставки
    private function setDeliveryRules(): void
    {
        //Если Новая почта (до отделения)
        if ($this->delivery_id === DeliveryMethodEnum::NovaPoshtaPickup()) {
            $this->rulesFromOutside[]['city_id'] = 'required';
            $this->rulesFromOutside[]['np_warehouse_id'] = 'required';
        }
        //Если Новая почта (адресная)
        if ($this->delivery_id === DeliveryMethodEnum::NovaPoshtaAddress()) {
            $this->rulesFromOutside[]['city_id'] = 'required';
            $this->rulesFromOutside[]['address'] = 'required';
        }
        //Если Самовывоз
        if ($this->delivery_id === DeliveryMethodEnum::SelfPickup()) {
            $this->rulesFromOutside[]['delivery_pickup_point_id'] = 'required';
        }
        //Если Курьер
        if ($this->delivery_id === DeliveryMethodEnum::Courier()) {
            $this->rulesFromOutside[]['receiver_first_name'] = 'required';
            $this->rulesFromOutside[]['receiver_last_name'] = 'required';
            $this->rulesFromOutside[]['receiver_phone'] = 'required';
            $this->rulesFromOutside[]['receiver_email'] = ['required', 'email'];
        }
        //TODO: при необходимости добавить нужные методы доставки
    }

    public function rendered(){
        $this->dispatch('checkout-checkout-initialized');
    }
}
