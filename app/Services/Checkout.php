<?php

namespace App\Services;

//TODO: Логика пересчета итоговых цен, дисконта и т.д.
//Потом эти цифры вставлять в чекаут, создание заказа.
//Перенести логику из app/Http/Requests/Order.php, app/Http/Controllers/Custom/CheckoutController.php,
//и использовать классы для дисконта и промокода

use App\Enums\OrderStatusEnum;
use App\Events\OrderCreate;
use App\Helpers\PhoneNumberHelper;
use App\Models\City;
use App\Models\Order as OrderModel;
use App\Models\LegalEntitiesRecipient;
use App\Models\PayMethod;
use App\Services\Checkouts\MonoPayParts\MonoPayPartsService;
use Gloudemans\Shoppingcart\Facades\Cart;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Checkout
{
    private DiscountCard $discountCardService;

    private Promocodes $promoCodeService;

    private Delivery $deliveryService;

    private Basket $basketService;

    private UserService $userService;

    private Payment $paymentService;

    public PayPartsCalculator $payPartsCalculator;

    private int $promoTotal = 0;

    private int $discountTotal = 0;

    private int|null $payPartsCount = 0;

    //Размер скидки в процентах, привязанной к примененной в чекауте дисконтной карте
    private ?int $discountCardSale = null;

    private array $userFields = ['email', 'phone', 'first_name', 'last_name', 'patronymic'];

    public function __construct(DiscountCard $discountCard, Promocodes $promoCode, Delivery $delivery, Basket $basket, UserService $userService, Payment $paymentService, PayPartsCalculator $payPartsCalculator)
    {
        $this->discountCardService = $discountCard;
        $this->promoCodeService = $promoCode;
        $this->deliveryService = $delivery;
        $this->basketService = $basket;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
        $this->payPartsCalculator = $payPartsCalculator;

        $this->discountCardSale = $this->getDiscountCardSale();
        $this->calculateSales();
    }

    public function getCartValues(): Collection
    {
        $this->calculateSales();

        $cartSubTotal = $this->getSubtotal();
        $promo = $this->getPromoCode();
        $delivery = $this->getDelivery();
        $cartDeliveryPrice = (int)$this->getDeliveryPrice();
        $cartDeliveryDesc = $this->getDeliveryDescription();
        $promoCode = optional($promo)->code ?? '';
        $promoSale = $this->getPromoSale();
        $promoSaleSum = $this->getPromoSaleSum();
        $discountSale = $this->getDiscountSale(); //Размер % скидки по дисконтной карте
        $discountSaleSum = $this->getDiscountSaleSum();
        $paymentTaxSum = $this->getPaymentTaxSum();
        $paymentTaxPercent = $this->getPaymentTaxPercent();
        $cartTotal = $this->getTotal() + $cartDeliveryPrice + $paymentTaxSum;

        return collect([
            'countProducts' => $this->countProducts(),
            'productsInCart' => $this->productsInCart()->reverse(),
            'payMethods' => $this->preparePayMethods(),
            'cartTotal' => $cartTotal,
            'cartSubTotal' => $cartSubTotal,
            'promo' => $promo,
            'promoCode' => $promoCode,
            'promoSale' => $promoSale,
            'promoSaleSum' => $promoSaleSum,
            'discountSale' => $discountSale,
            'discountSaleSum' => $discountSaleSum,
            'paymentTaxSum' => $paymentTaxSum,
            'paymentTaxPercent' => $paymentTaxPercent,
            'cartDeliveryPrice' => $cartDeliveryPrice,
            'cartDeliveryDesc' => $cartDeliveryDesc,
            'delivery' => $delivery,
            'isFbq' => (setting('checkbox_facebook_pixel') ?: 0)
        ])->merge($this->getUserData());
    }

    //Получить текущие данные пользователя для чекаута. Если он авторизован - берем его данные.
    // Если же он в чекауте уже модифицировал свои данные, загружаются измененные
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUserData() {
        //$user = app('user') ? collect(app('user')->only($this->userFields)) : collect();

        $user = $this->userService->getUserData()->only($this->userFields);

        $dataForm = session()->get('orderForm');

        return $dataForm ? $user->merge(collect($dataForm)->only($this->userFields)->reject('')) : $user;
    }

    public function getDelivery() {
        return $this->deliveryService->get();
    }

    public function getDeliveryPrice()
    {
        return $this->deliveryService->getPrice($this->getSubtotal());
    }

    public function getDeliveryDescription(): ?string
    {
        return $this->deliveryService->getPriceDescription($this->getSubtotal());
    }

    public function resetDelivery() {
        return $this->deliveryService->resetDelivery();
    }

    public function setDelivery(int $delivery_id): void
    {
        $this->deliveryService->setDelivery($delivery_id);
    }

    public function setPayment(int|null $payment_id): void
    {
        $this->paymentService->setPayment($payment_id);
    }

    public function getPayment()
    {
        return $this->paymentService->get();
    }

    public function setPayPartsCount($payPartsCount): void
    {
        $this->payPartsCount = $payPartsCount;
    }

    public function getPayPartsCount()
    {
        return $this->payPartsCount;
    }

    /**
     * Получение списка доставок для указанного города.
     */

    public function getDeliveries(int|string $cityId = null, bool $ignoreCity = false): Collection
    {
        return $this->deliveryService->getDeliveries($cityId, $ignoreCity);
    }

    //Получение полного списка методов доставки без учета выбора города
    public function getAllDeliveries()
    {
        return $this->deliveryService->getAllDeliveries();
    }

    //Вывод формы с полями для выбранного метода доставки
    public function getDeliveryPointers(int|string $city_id = null, int|string $delivery_id = null, string $delivery_type = null, mixed $errors = null): ?string
    {
        return $this->deliveryService->getDeliveryPointers($city_id, $delivery_id, $delivery_type, $errors);
    }

    //Получение списка отделений для выбранного типа доставки и города. А также поиск по нему по запросу
    public function getWarehouses(City $city, string $type = null, string $searchTerm = null): Collection
    {
        return $this->deliveryService->getWarehouses($city, $type, $searchTerm);
    }

    //Получение списка улиц для выбранного типа доставки и города. А также поиск по нему по запросу
    public function getStreets(City $city, string $type = null, string $searchTerm = null): Collection
    {
        return $this->deliveryService->getStreets($city, $type, $searchTerm);
    }

    public function getPromoCode()
    {
        return $this->promoCodeService->get();
    }

    //Проверить промокод по коду и если все хорошо, попробовать его применить
    public function checkPromocode($code)
    {
        return $this->promoCodeService->check($code);
    }

    //Сбросить текущий промокод
    public function resetPromocode()
    {
        return $this->promoCodeService->resetPromoCode();
    }

    //Размер скидки в процентах, привязанный к примененной в чекауте дисконтной карте
    public function getDiscountCardSale(): int
    {
        return $this->discountCardService->get();
    }

    /**
     * Доступен ли товар с рассрочкой моно или приват оплатой частями
     * @param $cartItem
     * @return bool - Да - кол-во платежей, Нет - false
     */
    public function item_is_installment($cartItem): bool
    {
        return $this->item_is_monopayparts($cartItem) || $this->item_is_privatpayparts($cartItem);
    }

    /**
     * Доступен ли товар с рассрочкой Монобанк
     * @param $cartItem
     * @return mixed - Да - кол-во платежей, Нет - false
     */
    public function item_is_monopayparts($cartItem): mixed
    {
        return $cartItem->model->getMonoPartsCount();
    }

    /**
     * Доступен ли товар с оплатой частями Приватбанк
     * @param $cartItem
     * @return mixed - Да - кол-во платежей, Нет - false
     */
    public function item_is_privatpayparts($cartItem): mixed
    {
        return $cartItem->model->getPrivatPartsCount();
    }

    /**
     * Является ли товар акционным - старая цена выше текущей
     * @param $cartItem
     * @return mixed - Да - кол-во платежей, Нет - false
     */
    public function item_is_promotional($cartItem): bool
    {
        $model = $cartItem->model;
        return $model->getPriceOld() > $cartItem->price;
    }

    /**
     * Количество товаров с рассрочкой моно в корзине оформления
     * @return int Количество или 0
     */
    public function monoPayPartsCountItems(): int
    {
        return $this->productsInCart()->reduce(function ($count, $cartItem) {
            if ($this->item_is_monopayparts($cartItem)) {
                $count++;
            }
            return $count;
        }, 0);
    }

    /**
     * Количество товаров с приват оплатой частями в корзине оформления
     * @return int Количество или 0
     */
    public function privatPayPartsCountItems(): int
    {
        return $this->productsInCart()->reduce(function ($count, $cartItem) {
            if ($this->item_is_privatpayparts($cartItem)) {
                $count++;
            }
            return $count;
        }, 0);
    }

    /**
     * Количество акционных товаров в корзине оформления
     * @return int Количество или 0
     */
    public function saleItemsCount(): int
    {
        return $this->productsInCart()->reduce(function ($count, $cartItem) {
            if ($this->item_is_promotional($cartItem)) {
                $count++;
            }
            return $count;
        }, 0);
    }

    /**
     * Доступна ли Моно-оплата частями в корзине по условиям
     * @return mixed Нет - false, Да - количество платежей
     */
    public function isShowMonoPayParts(): mixed
    {
        $monoItemsCount = 0; //Кол-во товаров с моно-рассрочкой;
        $parts = []; //Варианты кол-ва частей рассрочки
        $salesItemsCount = 0; //Кол-во товаров акционных

        foreach ($this->productsInCart() as $cartItem) {
            if ($partsCount = $this->item_is_monopayparts($cartItem)) {
                $monoItemsCount++;
                $parts[] = $partsCount;
            }
            //Если нужно запретить акционные товары, раскомментировать
            //if ($this->item_is_promotional($cartItem)) {
            //    $salesItemsCount++;
            //}
        }
        if (!empty($monoItemsCount) && //Если есть хотя бы один товар с моно-рассрочкой
            count(array_unique($parts)) === 1 && //Если только один вариант кол-ва платежей
            //$salesItemsCount === 0 && // Если нет акционных
            $this->countItems() === $monoItemsCount // Если в корзине нет товаров без Моно-рассрочки
            //empty($this->getPromoSaleSum()) && //Если нет скидки по промокоду
            //empty($this->getDiscountSaleSum()) //Если нет скидки по дисконту
        ) {
            return array_unique($parts)[0];
        }
        return false;
    }

    /**
     * Доступна ли Приват-оплата частями в корзине по условиям
     * @return mixed Нет - false, Да - количество платежей
     */
    public function isShowPrivatPayParts(): mixed
    {
        $privatItemsCount = 0; //Кол-во товаров с приват-рассрочкой;
        $parts = []; //Варианты кол-ва частей рассрочки
        $salesItemsCount = 0; //Кол-во товаров акционных

        foreach ($this->productsInCart() as $cartItem) {
            if ($partCount = $this->item_is_privatpayparts($cartItem)) {
                $privatItemsCount++;
                $parts[] = $partCount;
            }
            //Если нужно запретить акционные товары, раскомментировать
            //if ($this->item_is_promotional($cartItem)) {
            //    $salesItemsCount++;
            //}
        }
        if (!empty($privatItemsCount) && //Если есть хотя бы один товар с приват-рассрочкой
            count(array_unique($parts)) === 1 && //Если только один вариант кол-ва платежей
            //$salesItemsCount == 0 && // Если нет акционных
            $this->countItems() === $privatItemsCount // Если в корзине нет других товаров без Приват-рассрочки

            //empty($this->getPromoSaleSum()) && //Если нет скидки по промокоду
            //empty($this->getDiscountSaleSum()) //Если нет скидки по дисконту
        ) {
            return array_unique($parts)[0];
        }
        return false;
    }

    //Оригинал
    /*public function calculatePromoSaleSum(): int
    {
        $discountSale = $this->calculateDiscountSaleSum();
        $promocode = $this->promoCodeService->get();

        //если ест промокод && не применен дисконт или его разрешено применять с дисконтом
        if ($promocode && (!$discountSale || ($discountSale && $promocode->use_for_discount_cards == 1))) {
            $applicableProducts = $promocode->applicableProducts()->pluck('product_code')->toArray();

            return $this->productsInCart()->reduce(function ($sum, $cartItem) use ($promocode, $applicableProducts) {
                $isInstallment = $this->item_is_installment($cartItem);
                $isPromotional = $this->item_is_promotional($cartItem);

                //use_for_promotional - Применять к акционным товарам (старая цена больше текущей)
                //use_for_installments - Применять к товарам с рассрочкой/оплатой частями
                //use_for_discount_cards - Применять с дисконтными картами
                if (
                    ($isInstallment && $promocode->use_for_installments == 1 && $isPromotional && $promocode->use_for_promotional == 1) ||
                    ($isInstallment && $promocode->use_for_installments == 1 && !$isPromotional) ||
                    (!$isInstallment && $isPromotional && $promocode->use_for_promotional == 1) ||
                    (!$isInstallment && !$isPromotional)
                ) {
                    //товар только в списке промокода или у промокода вообще нет привязанных товаров
                    if (in_array(
                        $cartItem->model->code,
                        $applicableProducts
                    ) || $promocode->product_promocode->isEmpty()) {
                        $sum += $this->calculateSaleSum($cartItem, $promocode->sale);
                    }
                }

                return $sum;
            }, 0);
        }
        $this->promoCodeService->remove();
        return 0;
    }*/

    public function calculatePromoSaleSum(): int
    {
        $promoCode = $this->promoCodeService->get();

        //если ест промокод && не применен дисконт или его разрешено применять с дисконтом
        if ($promoCode/* && (!$discountSale || ($discountSale && $promoCode->use_for_discount_cards == 1))*/) {
            $applicableProducts = $promoCode->applicableProducts()->pluck('product_code')->toArray();

            $promoCodeSale = $this->getPromocodeCalculatedSale();

            return $this->productsInCart()->reduce(function ($sum, $cartItem) use ($promoCode, $promoCodeSale, $applicableProducts) {
                $isInstallment = $this->item_is_installment($cartItem);
                $isPromotional = $this->item_is_promotional($cartItem);

                //use_for_promotional - Применять к акционным товарам (старая цена больше текущей)
                //use_for_installments - Применять к товарам с рассрочкой/оплатой частями
                //use_for_discount_cards - Применять с дисконтными картами
                if (
                    ($isInstallment && $promoCode->use_for_installments === 1 && $isPromotional && $promoCode->use_for_promotional === 1) ||
                    ($isInstallment && $promoCode->use_for_installments === 1 && !$isPromotional) ||
                    (!$isInstallment && $isPromotional && $promoCode->use_for_promotional === 1) ||
                    (!$isInstallment && !$isPromotional)
                ) {
                    //товар только в списке промокода или у промокода вообще нет привязанных товаров
                    if (in_array($cartItem->model->code, $applicableProducts, true) || $promoCode->product_promocode->isEmpty()) {
                        $sum += $this->calculateSaleSum($cartItem, $promoCodeSale);
                    }
                }

                return $sum;
            }, 0);
        }
        $this->promoCodeService->remove();
        return 0;
    }

    //Пересчет всех данных со скидками
    public function calculateSales(): void
    {
        //$this->cart->setGlobalDiscount(0);
        $this->discountTotal = $this->calculateDiscountSaleSum();
        $this->promoTotal = $this->calculatePromoSaleSum();
    }

    public function getPromoSaleSum(): ?int
    {
        return $this->promoTotal;
    }

    public function getDiscountSaleSum(): ?int
    {
        return $this->discountTotal;
    }

    public function getSalePercent(): int
    {
        $discount = $this->getDiscountSale();
        $promo = $this->getPromoSale();

        return $discount + $promo;
    }

    //Оригинал
    /*public function calculateDiscountSaleSum(): int
    {
        $this->cartInstance()->setGlobalDiscount(0);

        if (($sale = $this->discountCardSale) && !$this->isInstallmentPaymentSelected()) {
            return $this->cartInstance()->content()->reduce(function ($sum, $cartItem) use ($sale) {
                return $sum + $this->calculateSaleSum($cartItem, $sale);
            }, 0);
        }

        return 0;
    }*/

    //Сумма скидки по дисконту
    public function calculateDiscountSaleSum(): int
    {
        $this->cartInstance()->setGlobalDiscount(0);

        $discountSale = $this->getDiscountCalculatedSale();

        if ($discountSale && !$this->isInstallmentPaymentSelected()) {
            return $this->productsInCart()->reduce(function ($sum, $cartItem) use ($discountSale) {
                return $sum + $this->calculateSaleSum($cartItem, $discountSale);
            }, 0);
        }

        return 0;
    }

    //Получаем размер скидки в % по дисконту, учитывая настройки промокода
    private function getDiscountCalculatedSale(): int|float
    {
        //надо учесть, можно ли применять дисконт с текщим промокодом
        $promoCode = $this->promoCodeService->get();
        $promoCodeSale = $this->promoCodeService->getSale();
        $discountSale = $this->discountCardService->get();
        $useWithDiscount = ($promoCode) ? ($promoCode->use_for_discount_cards ?: false) : true;

        if ($useWithDiscount) {
            return $this->discountCardService->get();
        }

        return ($discountSale > $promoCodeSale) ? $discountSale : 0;
    }

    //Получаем размер скидки в % по промокоду, учитывая его настройки
    public function getPromocodeCalculatedSale(): int|float
    {
        $promoCode = $this->promoCodeService->get();
        $promoCodeSale = $this->promoCodeService->getSale();
        $discountSale = $this->getDiscountCalculatedSale();

        $useWithDiscount = ($promoCode) ? ($promoCode->use_for_discount_cards ?: false) : true;

        if ($useWithDiscount) {
            return $promoCodeSale;
        }

        return ($discountSale > $promoCodeSale) ? 0 : $promoCodeSale;
    }

    /**
     * Вычисляет сумму скидки на товар.
     *
     * @param object $cartItem Объект CartItem элемент корзины для вычисления скидки.
     * @param float|int $discountPercent Процент скидки, которое применяется к товару.
     *
     * @return float|int Сумма скидки на товар.
     */
    private function calculateSaleSum($cartItem, float|int $discountPercent): float|int
    {
        // Применяем скидку только к товарам, у которых старая цена <= текущей
        if ($cartItem->baseAmount>1 && $cartItem->model->getPriceOld() <= $cartItem->price) {
            $discountPercentSum = $discountPercent + $cartItem->getDiscountRate();
            $this->cartInstance()->setDiscount($cartItem->rowId, $discountPercentSum);
            return ceil($cartItem->baseAmount * ($discountPercent / 100));
            //return ((($cartItem->price * $cartItem->qty) * $discountPercent) / 100);
        }
        return 0;
    }

    public function getSaleSum(): int
    {
        return $this->getPromoSaleSum() + $this->getDiscountSaleSum();
    }

    //Процент дисконта, если НЕ нужно обнулять в случае, если не применене ни к одной позиции в корзине
    /*public function getDiscountSale(): int
    {
        return $this->discountcard->get();
    }*/

    //Процент дисконта, если нужно обнулять в случае, если не применене ни к одной позиции в корзине.
    //Нужно предварительно запускать $this->calculateSales(); в нужном классе
    public function getDiscountSale(): int
    {
        if ($this->getDiscountSaleSum()) {
            return $this->discountCardSale;
        }

        return 0;
    }

    //Процент промокода, если НЕ нужно обнулять в случае, если не применене ни к одной позиции в корзине
    /*public function getPromoSale(): int
    {
        if ($code = $this->promocode->get()) {
            return $code->sale;
        }
        return 0;
    }*/

    //Процент промокода, если нужно обнулять в случае, если не применен ни к одной позиции в корзине.
    //Нужно предварительно запускать $this->calculateSales(); в нужном классе
    public function getPromoSale(): int|float
    {
        return ($this->promoTotal && $code = $this->promoCodeService->get()) ? $code->sale : 0;
    }

    //Возвращает чистый подитог цены всех позиций без скидок
    public function getSubtotal()
    {
        return $this->productsInCart()->sum(function ($item) {
            //return $item->price * $item->qty;
            return $item->baseAmount;
        });
    }

    public function getTotal(): int|float
    {
        $this->calculateSales();
        return $this->cartInstance()->total();
    }

    public function countProducts()
    {
        return $this->cartInstance()->count();
    }

    public function countItems()
    {
        return $this->cartInstance()->countItems();
    }

    public function productsInCart()
    {
        return $this->cartInstance()->content();
    }

    /**
     * Определяет, выбран ли в чекауте метод оплаты, подразумевающий Оплату частями
     */
    public function isInstallmentPaymentSelected(): bool
    {
        $payMethod = (int)request()->id ?: (int)request()->pay_method_id;
        if (in_array($payMethod, PayMethod::$paypartsMethodsId, true)) {
            return true;
        }
        return false;
    }


    private function preparePayMethods(int|null $delivery_id = 0): Collection
    {

        $payMethods = PayMethod::forDelivery($delivery_id)
            ->active()
            ->greaterThanAmount($this->getTotal())
            ->orderPriority()
            ->get();

        $payMethodsResult = [];

        foreach ($payMethods as $payMethod) {
            //Проверяем доп.условия для показа методов оплаты
            $payMethodName = $payMethod->checkout->slug ?? '';
            switch ($payMethodName) {
                case 'monopayparts': //
                    //23.03.2024 Отключена валидация, т.к. срабатывает при каждой перезагрузке.
                    //Достаточно проверки в методе getPaymentData контроллера CheckoutController
                    //$response = $this->monoVerifyPhone() ?: '';
                    //$responseArray = is_array($response) ? $response : [];
                    //$hidden = @$responseArray['found'] !== true;
                    //$message = (!$hidden) ? '' : (@$responseArray['message'] ?? '') ;
                    $hidden = !$this->isPayPartsWithPromocodeApplicable(); //TODO: универсальный способ проверки условия доступности
                    $message = $hidden ? session('payparts_message') : '';  //TODO: универсальный способ вывода сообщения
                    $partscount = $this->isShowMonoPayParts(); //Доступен ли в метод оплаты в списке
                    break;
                case 'liqpaycod':
                    $hidden = $this->hasPostPaymentDisabledItems();
                    $message = ''; //При hidden==true в шаблоне метода оплаты определено сообщение по-умолчанию. Здесь можно переопределить
                    $partscount = true;
                    break;
                case 'privatpayparts': //
                    $hidden = !$this->isPayPartsWithPromocodeApplicable();  //TODO: универсальный способ проверки условия доступности
                    $message = $hidden ? session('payparts_message') : ''; //TODO: универсальный способ вывода сообщенияы
                    $partscount = $this->isShowPrivatPayParts();
                    break;
                default:
                    $hidden = false;
                    $message = '';
                    $partscount = true;
                    break;
            }
            if (!$partscount) {
                continue;
            }
            //---

            $payMethodsResult[] = [
                'name' => $payMethod->t('title'),
                'id' => $payMethod->id,
                'picture' => $payMethod->picture,
                'short_description' => $payMethod->t('short_description'),
                'slug' => $payMethodName,
                'hidden' => $hidden,
                'message' => $message,
                'availablePartsCount' => $partscount,
            ];
        }

        return collect($payMethodsResult);
    }

    public function _getPayments(int|null $delivery_id = 0)
    {
        $payments = $this->preparePayMethods($delivery_id)->toArray();

        $pay_method_id = $this->getPayment()?->id ?? 0;

        return ($payments) ? view('livewire.checkout.forms.payments', compact('payments', 'pay_method_id')
        )->render() : __t('Для этого метода доставки нет методов оплаты');
    }

    public function getPayments(int|null $delivery_id = 0)
    {
        return $this->preparePayMethods($delivery_id)->toArray();
    }

    //Возвращает array с html-блоками данных выбранного метода оплаты: для итоговых цен корзины.
    //Напр., для ОЧ выводим таблицу рассчета цены и select с частями + выбранный текущий. И т.д.
    //Далее можно использовать в js
    public function paymentForms($payment_id, $payparts_count): array
    {
        $payment = $this->getPaymentData($payment_id, $payparts_count);

        if ($payment_id && !empty($payment)) {
            $cart = 'checkout.partials.forms.payment_cart_' . $payment_id;
            $form = 'checkout.partials.forms.payment_details_' . $payment_id;
            return [
                'cart' => (view()->exists($cart)) ? view($cart, compact('payment'))->render() : '',
                'form' => (view()->exists($form)) ? view($form, compact('payment'))->render() : '',
            ];
        }
        return [];
    }

    //Принимает ИД метода оплаты и кол-во частей ОП = выводит массив для построения view для указанного метода оплаты
    private function getPaymentData($payment_id, $cur_partscount): array
    {
        $payment = [];

        if ($payment_id) {
            $payMethod = PayMethod::with('checkout')->where('id', $payment_id)->first();
            $payMethodName = $payMethod->checkout->slug ?? '';

            switch ($payMethodName) {
                case 'monopayparts':
                    $response = $this->monoVerifyPhone();
                    $responseArray = is_array($response) ? $response : [];
                    $payment['hidden'] = @$responseArray['found'] !== true;
                    $payment['message'] = !$payment['hidden'] ? '' : ($responseArray['message'] ?? '');
                    $payment['partscount'] = $this->isShowMonoPayParts();
                    break;
                case 'privatpayparts':
                    $payment['hidden'] = false;
                    $payment['message'] = '';
                    $payment['partscount'] = $this->isShowPrivatPayParts();
                    break;
                default:
                    $payment['hidden'] = false;
                    $payment['message'] = '';
                    $payment['partscount'] = 0;
                    break;
            }

            if ($session_payment = session()->get('payparts')) {
                $payment['pay_method_id'] = $session_payment['pay_method_id'];
                $payment['payparts_count'] = $session_payment['payparts_count'];
                if ($payment_id !== $session_payment['pay_method_id']) {
                    session()->remove('payparts');
                    unset($payment['pay_method_id'], $payment['payparts_count']);
                }
            }

            $payment['total'] = $this->getTotal() + (int)$this->getDeliveryPrice();
            $payment['cur_partscount'] = ($cur_partscount) ?: ((isset($payment['payparts_count'])) ? $payment['payparts_count'] : $payment['partscount']);
            $payment['short_description'] = $payMethod->t('short_description');
        }

        return $payment;
    }

    /*
    public function monoVerifyPhone()
    {
        $form = session()->get('orderForm');
        $response = (isset($form['phone'])) ? (new MonoPayPartsService())->validatePhone($form['phone'])->getBody()->getContents() : '{}';
        return json_decode($response, true);
    }*/
    /*public function monoVerifyPhone()
    {
        $form = session()->get('orderForm');
        $phone = $form['phone'] ?? null;

        if ($phone) {
            $monoService = new MonoPayPartsService();
            $response = $monoService->validatePhone($phone);

            if ($response->getStatusCode() === 200) {
                $contents = $response->getBody()->getContents();
                return json_decode($contents, true);
            }
        }

        return [];
    }*/

    public function monoVerifyPhone(): ?array
    {
        $form = session()->get('orderForm');
        $phone = $form['phone'] ?? null;
        $phone = PhoneNumberHelper::clean($phone);

        if (!$phone) {
            return [];
        }

        // Генерация уникального ключа на основе номера телефона
        $cacheKey = 'monoVerifyPhone:' . md5($phone);

        try {
            // Попытка получить результат из кеша с использованием тега
            //return Cache::tags(['monoVerifyPhone'])->remember($cacheKey, 60, function () use ($phone) {
                $monoService = new MonoPayPartsService();
                $response = $monoService->validatePhone($phone);

                if ($response->getStatusCode() === 200) {
                    $contents = $response->getBody()->getContents();
                    Log::info('monoVerifyPhone '.$phone.': '.$contents);
                    return json_decode($contents, true);
                }

                return [];
            //});
        } catch (RequestException $e) {
            // Обработка ошибки запроса (например, логгирование)
            Log::error('Mono API Request Exception '.$phone.': ' . $e->getMessage());
            return [];
        }
    }

    private function cartInstance()
    {
        return $this->basketService->getCart();
    }


    //Подготавливаем данные для записи заказа в БД
    public function prepareOrderData($orderFields)
    {
        $this->calculateSales();

        $orderFields['receiver_phone'] = PhoneNumberHelper::clean($orderFields['receiver_phone']);
        $orderFields['phone'] = PhoneNumberHelper::clean($orderFields['phone']);

        return collect($orderFields)->merge([
            'user_id' => $this->userService->getUserOrCreate($orderFields, $orderFields['register_me']),
            'receiver' => $this->getReceiverType($orderFields['receiver']),
            'order_status_id' => OrderStatusEnum::New(),
            'cost' => $this->getCost(),
            'sale' => $this->getSalePercent(),
            'sale_promo' => $this->getPromoSale(),
            'sale_discount' => $this->getDiscountSale(),
            'tax' => $this->getOrderTax(),
            //'sale_promo_amount' => $this->getPromoSaleSum(),
            //'sale_discount_amount' => $this->getDiscountSaleSum(),
            //--
            'cost_without_sale' => $this->getSubtotal(),
            'legal_entities_recipient_id' => $this->getRequisitesId(),
            //'num' => $this->genNumOrder()
        ])->toArray();
    }

    //Создание нового заказа в БД
    public function createOrder($orderData): OrderModel
    {
        $data = $this->prepareOrderData($orderData);

        $order = OrderModel::create($data)->afterSave();

        $this->cartInstance()->destroy();

        app(UnfinishedBasketService::class)->delete();

        session()->flash('order', $order);
        session()->put(["order_id" => $order->id]);
        \Cookie::queue('order_id', $order->id, 5000);

        $this->promoCodeService->remove();

        OrderCreate::dispatch($order); //Здесь в Listeners/Order/SendToGA4.php передаем для GA4

        return $order;
    }

    //Получаем тип получателя посылки
    protected function getReceiverType($receiver): string
    {
        return (empty($receiver) || $receiver=='user') ? 'user' : 'other';
    }

    //Получаем ИД юр.реквизитов по-умолчанию
    protected function getRequisitesId()
    {
        return LegalEntitiesRecipient::where('is_default', 1)->value('id');
    }

    //Доступен ли ТЕКУЩИЙ ВЫБРАННЫЙ метод оплаты для работы с промокодом.
    public function isSelectedPaymentWithPromocodeApplicable(): bool
    {
        //Если выбран метод оплаты, который подразумевает оплату частями
        if ($this->isInstallmentPaymentSelected()) {

            if ($this->promoCodeService->get()) {
                if ($this->promoCodeService->get()->use_for_installments) {
                    return true;
                }

                $this->promoCodeService->remove();
                return false;
            }

            return true;
        }
        //Можно добавить другую проверку. Пока методы без ОЧ не проверяем

        return true;
    }

    //Позволяют ли условия промокода активировать методы оплаты, подразумевающие оплату частями.
    public function isPayPartsWithPromocodeApplicable(): bool
    {
        $promoCode = $this->promoCodeService->get();

        if ($promoCode) {
            if ($promoCode->use_for_installments==1) {
                return true;
            }

            session()->flash('payparts_message', __t('Даний метод оплати недоступний із застосованим промокодом'));
            return false;
        }

        return true;
    }

    /**
     * Проверяет наличие хотя бы одного товарова с отключенной постоплатой в корзине.
     *
     * @return bool Возвращает true, если хотя бы один товар с отключенной постоплатой присутствует в корзине, в противном случае возвращает false.
     */
    public function hasPostPaymentDisabledItems(): bool
    {
        return Cart::content()->contains(function ($cartItem) {
            return $cartItem->model->postpayment_disabled;
        });
    }

    //Получаем данные о рассчете оплаты частями для товара в корзине
    //Работает, только если в корзине одно наименование товара
    public function getCurrentPayPartsData()
    {
        return once(function () {
            if($this->getPayPartsCount()>0 && $this->countItems()==1) {
                return $this->payPartsCalculator->calculatePriceDetails($this->productsInCart()->first()->model,
                    $this->getPayPartsCount(), $this->getPayment());
            }
            return null;
        });
    }

    /**
     * Рассчитывает сумму налога на оплату (комиссию).
     *
     * Функция использует процент комиссии и общую сумму,
     * чтобы вычислить итоговую сумму налога на оплату.
     *
     * @return float Сумма налога на оплату, округлённая до ближайшего целого значения.
     */
    private function getPaymentTaxSum()
    {
        $commission_rate = $this->getPaymentTaxPercent();

        $total = $this->getTotal();
        return ceil(($total * $commission_rate)/100);
    }

    /**
     * Возвращает процент налога на оплату (комиссии).
     *
     * Функция извлекает процент комиссии из объекта оплаты, если объект существует.
     * Если процент комиссии не установлен или объект не найден, возвращает 0.
     *
     * @return float Процент налога на оплату (комиссии).
     */
    private function getPaymentTaxPercent()
    {
        $payment = $this->getPayment();

        // Проверяем, что $payment - это объект PayMethod, а не коллекция
        if ($payment instanceof \App\Models\PayMethod) {
            //$result = $payment->commission_rate ?? 0;
            $result = $payment->is_payparts ? ($this->getCurrentPayPartsData()['commissionPercentage'] ?? 0) : ($payment->commission_rate ?? 0);
            return (float)$result;
        }

        return 0;
    }

    //Возвращаем итоговую сумму всех наценок на сумму ЗАКАЗА (без учета персональных наценок каждого товара)
    //Поскольку наценка для каждого товара считается в самом товаре и отражается в цене товара, ее не учитываем.
    //Получаем общую сумму дополнительных наценок, напр., комиссия платежной системы, НДС и т.д.
    //Далее эту сумму нужно приплюсовать к итоговой сумме заказа и также записать в отдельное поле таблицы заказа
    //В теперешнем варианте используется только комиссия платежной системы, но можно добавить другие
    private function getOrderTax()
    {
        return $this->getPaymentTaxSum();
    }

    //Возвращает конечную стоимость заказа, которую можно записывать в БД
    private function getCost()
    {
        return $this->getTotal() + $this->getOrderTax();
    }
}
