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

class Checkout extends Component
{
    #[Locked]
    private CheckoutService $checkoutService;

    //Учитывать ли город для всего чекаута глобально. Если true, то город не сбрасывается при смене метода доставки и т.д. Он выбирается первым и далее все поля учитывают город
    //Если false, то при смене метода доставки город сбрасывается (подразумевается, что город должен выбираться в рамках выбранного метода доставки)
    public bool|string|null $cityIsGlobal = true;

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
    public string|null $phone = '';

    #[Validate('required')]
    #[Validate('email')]
    public string|null $email = null;

    #[Validate('required')]
    public string|null $patronymic = null;

    public bool $receiver = false;
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
    public string|int|null $delivery_id = 3;
    public string|int|null $delivery_type = 'np';
    public string|int|null $payparts_count = null;
    public string|int|null $np_warehouse_id = null;
    public string|int|null $delivery_pickup_point_id = null;
    public string|int|null $ukrposhta_warehouse_id = null;
    public string|int|null $justin_warehouse_id = null;
    public string|int|null $meest_warehouse_id = null;

    public string|null $address = null; //Адрес
    public $street = null; //Улица
    public string|null $house = null; //Дом
    public string|null $apartment = null; //Квартира
    public string|null $building = null; //Корпус
    public string|null $floor = null; //Этаж
    public bool|null $is_elevator = false; //Есть лифт
    public bool|null $is_lifting = false; //Нужна услуга поднятия на этаж
    public bool|null $other_street = false; //TRUE - Если улица в списке не найдена и нужно ввести ее название вручную в поле address

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

    public function mount($cityIsGlobal = true): void
    {
        //Преобразуем параметр "глобальности" города в булев тип
        $this->cityIsGlobal = !(($cityIsGlobal === 'false' || $cityIsGlobal === false));

        //Установка данных пользователя
        $this->loadUserProperty();

        //Установка метода доставки по-умолчанию
        $this->setDefaultDelivery();

        //Сброс метода оплаты
        $this->checkoutService->setPayment(0);

    }

    #[On('cart-changed')]
    public function reload()
    {
        return true;
    }

    #[On('checkout-set-property')]
    public function updating($property, $value): void
    {
        //$this->saveOrderField($property, $value);

        if(is_array($value) && isset($value['value'])) {
            $value = $value['value'];
        }
        if(is_array($value)) {
            $value=array_values($value);
        }

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

        //$this->dispatch('cart-changed'); //<--
        //$this->validate($this->getRules()); //<--

    }

    //Устанавливаем город
    public function setCity($value)
    {
        $this->setProperty('city_id', $value);
    }

    //Устанавливаем свойство
    //#[On('checkout-set-property')]
    public function setProperty($property, $value): void
    {
        $this->{$property} = $value;

        $this->saveOrderField($property, $value);

        //Применяем правила валидации в зависимости от выбранных параметров
        $this->setDeliveryRules();

        //При смене города сбрасываем способы доставки и перезагружаем город (чтобы принудительно перезагрузить вложенные компоненты)
        if($property === 'city_id') {
            $this->resetDeliveryFields();
            $this->reloadCity();
        }

        //При смене метода доставки сбрасываем методы доставки и город ( если он не глобальный, т.е. не выбирается для всего чекаута)
        if ($property === 'delivery_id') {
            $this->resetDeliveryFields();
            if (!$this->cityIsGlobal) {
                $this->city_id = null;
            }
        }

        //При выборе Улицы нет в списке - сброс ранее выбранной улицы
        if ($property === 'other_street' && !empty($value)) {
            $this->street = null;
        }

        //При вводе адресных полей собираем их в поле адреса
        //Если это не нужно, можно закомментировать
        //if (in_array($property, ['street', 'house', 'apartment', 'building', 'floor'])) {
        //    $this->address = $this->formatAddress();
        //}
    }

    /**
     * Объединяет непустые адресные части в одну строку, разделённую запятыми.
     *
     * @return string Объединённая строка адреса.
     */
    private function formatAddress(): string
    {
        $address = [
            'street' => $this->street ? __t('ул.'). ' '.$this->street : '',
            'house' => $this->house ? __t('дом'). ' '.$this->house : '',
            'appartment' => $this->apartment ? __t('кв.'). ' '.$this->apartment : '',
            'building' => $this->building ? __t('корпус'). ' '.$this->building : '',
            'floor' => $this->floor ? __t('этаж'). ' '.$this->floor : ''
        ];

        return implode(', ', array_filter($address));
    }

    //Принудительная перезагрузка города (чтобы дочерние компоненты могли перерисоваться)
    private function reloadCity()
    {
        $city_id = $this->city_id;
        $this->reset('city_id');
        $this->dispatch('checkout-city-changed', city_id: $city_id);
    }

    #[On('checkout-city-changed')]
    public function updatingCityId($city_id)
    {
        $this->city_id = $city_id;
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
        //$this->validate($this->getRules());

    }

    //Выбор метода оплаты при клике на чекбокс
    public function selectPayment($payment_id): void
    {
        $this->checkoutService->setPayment($payment_id);
        $this->updating('pay_method_id', $payment_id);
        $this->setDeliveryRules();
        $this->validate($this->getRules());
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

        //Редиректим на Спасибо за заказ в любом случае, а далее уже в шаблоне редиректим на оплату, если нужно
        //В шаблоне resources/views/checkout/complete.blade.php проверяем  !empty($order->pay()) && !empty(request()->get('redirect'))
        if ($order->payMethod?->checkout) {
            //return redirect()->to($order->urlPayment()); //прямой редирект на оплату
            return redirect()->route('checkout.complete', ['redirect' => '1']);
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

        $this->deliveries = $this->checkoutService->getDeliveries($this->city_id, !$this->cityIsGlobal); //второй параметр - игнорировать ли выбор города
        $this->delivery_form = $this->checkoutService->getDeliveryPointers($this->city_id, $this->delivery_id, $this->delivery_type, $this->checkoutErrors);
        $this->payments = $this->checkoutService->getPayments($this->delivery_id);
        return view('livewire.checkout.checkout')->with($checkout);
    }

    //Возвращаем флаг, пройден ли указанный шаг заполнения формы
    private function isStepSuccess(int $step = null)
    {
        $errors = $this->getErrorBag();

        switch ($step) {
            case 1:
                return !$errors->has('last_name') && !$errors->has('first_name') && !$errors->has('phone') && !$errors->has('email') && !$errors->has('patronymic') && !empty($this->city_id);
                break;
            case 2:

                break;
            case 3:
                return !$errors->has('pay_method_id');
                break;
        }
        return false;
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
                'street',
                'house',
                'building',
                'apartment',
                'floor',
                'is_lifting',
                'is_elevator',
                'ukrposhta_warehouse_id',
                'justin_warehouse_id',
                'meest_warehouse_id',
                'receiver',
                'receiver_first_name',
                'receiver_last_name',
                'receiver_patronymic_name',
                'receiver_phone',
                'receiver_email',
                'pay_method_id',
                'other_street'
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
        //Если Новая почта (адресная) или Курьер
        if (
            $this->delivery_id === DeliveryMethodEnum::NovaPoshtaAddress() ||
            $this->delivery_id === DeliveryMethodEnum::Courier() ) {
            $this->rulesFromOutside[]['city_id'] = 'required';
            if($this->other_street) {
                $this->rulesFromOutside[]['address'] = 'required';
            } else {
                $this->rulesFromOutside[]['street'] = 'required';
            }
            $this->rulesFromOutside[]['house'] = 'required';
        }
        //Если Самовывоз
        if ($this->delivery_id === DeliveryMethodEnum::SelfPickup()) {
            $this->rulesFromOutside[]['delivery_pickup_point_id'] = 'required';
        }
        //TODO: при необходимости добавить нужные методы доставки
        //Если другой получатель
        if($this->receiver) {
            $this->rulesFromOutside[]['receiver_first_name'] = 'required';
            $this->rulesFromOutside[]['receiver_last_name'] = 'required';
            $this->rulesFromOutside[]['receiver_patronymic_name'] = 'required';
            $this->rulesFromOutside[]['receiver_phone'] = 'required';
        }
    }

    //Установка метода доставки по-умолчанию
    private function setDefaultDelivery()
    {
        //Если город глобальный + выбран или Не глобальный - устанавливаем метод оплаты по-умолчанию.
        if (($this->cityIsGlobal && !empty($this->city_id)) || !$this->cityIsGlobal) {
            $this->selectDelivery($this->delivery_id, $this->delivery_type);
        }
    }
}
