<?php

namespace App\Http\Controllers\Custom;

use App\Events\OrderCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckPromoCodeRequest;
use App\Http\Requests\Order as OrderRequest;
use App\Jobs\StoreUnfinishedBasket;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryPickupPoint;
use App\Models\Order;
use App\Models\PayMethod;
use App\Services\Analytics;
use App\Services\Basket;
use App\Services\Checkout;
use App\Services\Checkouts\MonoPayParts\MonoPayPartsService;
use App\Services\DeliveryTime;
use App\Services\Promocodes;
use App\Services\UnfinishedBasketService;
use App\Services\UtmLabel;
use Gloudemans\Shoppingcart\Cart as Cart2;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private UnfinishedBasketService $unfinishedBasketService;

    private $promocode;

    private $checkoutService;

    private $basket;

    /**
     * Кол-во удаленного/измененого/добавленого товара для аналитики
     * @var int
     */
    private $qty;

    private $product;

    /**
     * Название действия для аналитики
     * @var string
     */
    private $action;

    //TODO: сервисы Promocodes, DiscountCard, Basket объединить в один Checkout и обращаться к нему
    public function __construct(
        UnfinishedBasketService $unfinishedBasketService,
        Promocodes $promocode,
        Checkout $checkoutService,
        Basket $basket
    ) {
        $this->unfinishedBasketService = $unfinishedBasketService;

        $this->promocode = $promocode;
        $this->checkoutService = $checkoutService;
        $this->basket = $basket;
        $this->checkoutService->calculateSales();
    }

    public function index(): View|RedirectResponse
    {
        //Если нужно сохранять данные о доставке после перезагрузке страницы, закомментировать
        $this->checkoutService->resetDelivery();

        //Актуализируем состав корзины
        $this->basket->updateAvailabilityAndPrices();

        if (!$this->checkoutService->countProducts()) {
            return redirect('/');
        }

        $analytic = new Analytics($this->checkoutService->productsInCart()->values());

        $googleGA4Analytics = $analytic->googleGA4product();

        return view('checkout.index', compact('googleGA4Analytics'));
    }

    public function getCart(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }

    public function getProducts(): Collection
    {
        return $this->prepareProducts();
    }

    public function complete(): View|RedirectResponse
    {
        //$order = session('order');
        $order_id = session('order_id') ?: request()->cookie('order_id');

        if (!$order_id) {
            return redirect('/');
        }

        $order = Order::find($order_id);

        $items = $order->cartOrderProducts;

        $analytic = new Analytics($items);

        $universalAnalytics = $analytic->universal();

        $googleAnalytics = $analytic->google();

        $dynamicAnalytics = $analytic->dynamic();

        $googleGA4Analytics = $analytic->googleGA4();

        $googleGA4User = $analytic->googleGA4User($order);

        //Для GA4 передается в Listeners/Order/SendToGA4.php

        return view('checkout.complete', compact('order', 'universalAnalytics', 'googleAnalytics', 'dynamicAnalytics', 'googleGA4Analytics', 'googleGA4User'));
        //return view('checkout.complete', compact('order'));
    }

    public function send(OrderRequest $request, UtmLabel $utmLabel): RedirectResponse
    {
        $data = $request->except(['g-recaptcha-response', 'g_recaptcha_response', '_token']);

        $order = Order::create($data)->afterSave();

        Cart::destroy();

        app(UnfinishedBasketService::class)->delete();

        $request->session()->flash('order', $order);

        $this->promocode->remove();

        OrderCreate::dispatch($order); //Здесь в Listeners/Order/SendToGA4.php передаем для GA4

        if ($order->payMethod->checkout) {
            return redirect()->to($order->urlPayment());
        }

        return redirect()->route('checkout.complete');
    }

    private function getDelivery()
    {
        return session()->get('delivery');
    }

    private function getDeliveryPrice()
    {
        if ($delivery = $this->getDelivery()) {
            return (($this->checkoutService->getSubtotal() >= $delivery->free_cost) && !is_null($delivery->free_cost)) ? 'free' : $delivery->price;
        }

        return 0;
    }

    private function getDeliveryDesc()
    {
        $delivery = $this->getDelivery();

        return ($this->getDeliveryPrice() === 'free') ? __t('бесплатно') : (($delivery) ? strip_tags($this->getDelivery()->t('description')) : '');
    }

    public function removeDelivery(): JsonResponse
    {
        $this->checkoutService->resetDelivery();

        return response()->json([
            'status' => 'success',
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }

    public function checkPromoCode(CheckPromoCodeRequest $request): JsonResponse
    {
        $code = $this->promocode->check($request->get('code'));
        $promoSaleSum = ($code) ? $this->checkoutService->calculatePromoSaleSum() : 0;

        if ($promoSaleSum > 0) {
            return response()->json([
                'status' => 'success',
                'sale' => $code->sale ?? 0,
                'html' => $this->getHtmlCheckoutBasket(),
            ]);
        }

        $this->promocode->resetPromoCode();

        if ($code && !$promoSaleSum) {
            return response()->json([
                'status' => 'error',
                'message' => __t('Введенный вами промокод не применим к выбранным товарам. Просмотрите условия использования промокода'),
                'html' => $this->getHtmlCheckoutBasket(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __t('Промо код не найден'),
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }


    public function resetPromoCode(): JsonResponse
    {
        if ($this->promocode->resetPromoCode()) {
            return response()->json([
                'status' => 'success',
                'html' => $this->getHtmlCheckoutBasket(),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __t('Промокод не удалось отменить. Попробуйте немного позже'),
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }

    public function searchCities(): Collection
    {
        $query = mb_convert_case(request('q'), MB_CASE_TITLE, 'UTF-8');

        $cities = City::where('title->ua', 'like', $query . '%')
            ->orWhere('title->ru', 'like', $query . '%')
            ->orWhere('title->en', 'like', $query . '%')
            ->orderBy('title->' . App::getLocale(), 'asc')->get();

        return $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'title' => $city->t('origin')?:$city->t('title'),
                'value' => $city->id,
                'label' => $city->t('origin')?:$city->t('title'),
            ];
        });
    }

    public function getDeliveries(): array
    {
        $city = City::find(request('city'));

        $defaultDeliveries = Delivery::where('is_show_for_all_cities', 1)->active()->orderBy('priority')->get();

        $deliveries = $city->deliveries()->active()->get()->merge($defaultDeliveries);

        $deliveryTerm = $this->getDeliveryTerm();

        return $deliveries->sortBy('priority')->map(function ($delivery) use ($deliveryTerm) {
            return [
                'id' => $delivery->id,
                'title' => $delivery->t('title'),
                'price' => $delivery->price,
                'description' => $delivery->t('description'),
                'type' => $delivery->type,
                //'term' => $deliveryTerm,
                'term' => (@$deliveryTerm[$delivery->id]['description']) ?? '',
            ];
        })->toArray();
    }

    //Срок отправки. Если все товары готовы к отправке - Сегодня. Иначе - по сроку поступления
    public function getDeliveryTerm(): array|string
    {
        $ids = $this->getIdProductsIncart();
        if (!$ids) {
            return '';
        }

        return (new DeliveryTime())->getDeliveryTimeForProducts($ids);
    }

    private function getIdProductsIncart()
    {
        return Cart::content()->map(function ($item, $key) {
            return $item->id;
        })->toArray();
    }


    //Получаем список всех отделений города (ИД в запросе) по указанному типу доставки и ее номеру - вместе с их формами
    //Если не указан тип (алиас) метода доставки, получаем его по ИД метода доставки, переданного в запросе
    public function getDeliveryPointers(): JsonResponse
    {
        $type = request('type');
        $id = request('id');

        $delivery = Delivery::find($id)->first();

        if (!$delivery) {
            return response()->json([
                'status' => 'error',
                'html' => __t('Метод доставки не найден'),
                'results' => []
            ]);
        }

        $type = ($type) ? : $delivery->type;

        //Устанавливаем данные о выбранной доставке. Для пересчета цены доставки в чекауте
        $this->checkoutService->setDelivery($id);

        switch ($type) {
            case 'pickup':
                $points = $this->getDeliveryPickupPointers();
                break;
            default:
                //$points = $this->getDeliveryNPPointers();
                $points = $this->getDeliveryWarehousesPointers();
                break;
        }

        $results = $points->map(function ($point) {
            return [
                'id' => $point['id'],
                'title' => $point['title'],
            ];
        });

        $html = ($id) ? view(
            'checkout.partials.forms.delivery_' . $id,
            compact('points')
        )->render() : __t('Для этого метода доставки нет отделений');

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'results' => $results
        ]);
    }

//--

    //Получаем список всех пунктов самовывоза.
    //Используется в getDeliveryPointers() для формирования списка отделений вместе с формой.
    //Но можно использовать самостоятельно для формирования только списка отделений без форм
    public function getDeliveryPickupPointers(): Collection
    {
        $cityId = request('city', '');

        $query = DeliveryPickupPoint::orderBy('priority', 'asc');

        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }

        return $query->get()->map(function ($point) {
            return [
                'id' => $point->id,
                'num' => $point->num,
                'title' => $point->t('address'),
                'text' => '',
            ];
        });
    }

    //Получаем список всех отделений Новой почты (до отделения) в указанном городе.
    //Используется в getDeliveryPointers() для формирования списка отделений вместе с формой.
    //Но можно использовать самостоятельно для формирования только списка отделений без форм
    public function getDeliveryNPPointers(): Collection
    {
        $cityName = request('city');
        $searchTerm = request('q');
        $type = request('type') ?? 'np';

        $city = City::find($cityName);

        return $this->checkoutService->getWarehouses($city, $type, $searchTerm);
    }

    //Получаем список улиц Новой почты в указанном городе.
    public function getDeliveryNPStreets(): Collection
    {
        $cityName = request('city');
        $searchTerm = request('q');
        $type = request('type') ?? 'np';

        $city = City::find($cityName);

        return $this->checkoutService->getStreets($city, $type, $searchTerm);
    }

    //Получаем список всех отделений по переданному городу и типу(алиасу доставки). Для методов доставки, кроме самовывоза.
    //Используется в getDeliveryPointers() для формирования списка отделений вместе с формой.
    //Но можно использовать самостоятельно для формирования только списка отделений без форм
    public function getDeliveryWarehousesPointers(): Collection
    {
        $cityName = request('city');
        $searchTerm = request('q');
        $aliasDelivery = request('type');

        $city = City::find($cityName);

        return $this->checkoutService->getWarehouses($city, $aliasDelivery, $searchTerm);
    }

    //Получаем список отделений любого типа доставки - по ее типу (алиасу) + городу + поиск. Возвращает только список без форм
    public function getWarehouses(string $type, City $city): Collection
    {
        $searchTerm = request('q');

        return $this->checkoutService->getWarehouses($city, $type, $searchTerm);
    }

    private function preparePayMethods(int $delivery_id = 0): Collection
    {
        $payMethods = PayMethod::forDelivery($delivery_id)
            ->active()
            ->greaterThanAmount($this->checkoutService->getTotal())
            ->orderPriority()
            ->get();

        $payMethodsResult = [];

        foreach ($payMethods as $payMethod) {
            //Проверяем доп.условия для показа методов оплаты
            $payMethodName = $payMethod->checkout->slug ?? '';
            switch ($payMethodName) {
                case 'monopayparts': //
                    $response = $this->monoVerifyPhone();
                    $hidden = @$response['found'] != true;
                    $message = (!$hidden) ? '' : @$response['message'];
                    $partscount = $this->checkoutService->isShowMonoPayParts();
                    break;
                case 'privatpayparts': //
                    $hidden = false;
                    $message = '';
                    $partscount = $this->checkoutService->isShowPrivatPayParts();
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
                'short_description' => $payMethod->short_description,
                'slug' => $payMethodName,
                'hidden' => $hidden,
                'message' => $message,
                'partscount' => $partscount,
            ];
        }

        return collect($payMethodsResult);
    }

    private function prepareProducts(): Collection
    {
        $productsCart = Cart::content();
        $products = [];

        foreach ($productsCart as $product) {
            $model = $product->model;
            $products[] = [
                'id' => $product->rowId,
                'img_link' => $model->getUrl(),
                'img_src' => $model->getImgPath(115, 115),
                'title' => $model->t('title'),
                'price' => $product->price,
                'count' => $product->qty,
                'total' => $product->price * $product->qty,
                'product_id' => $product->id,
                'options' => $this->getOptions($product->options),
            ];
        }

        return collect($products);
    }

    public function getPayments(): JsonResponse
    {
        $payments = $this->preparePayMethods(request('id'))->toArray();

        $session_payment = session()->get('payparts'); //Берем из сессии метод оплаты и кол-во частей

        $html = ($payments) ? view(
            'checkout.partials.forms.payments',
            compact('payments', 'session_payment')
        )->render() : __t('Для этого метода доставки нет методов оплаты');

        return response()->json([
            'status' => 'success',
            'html' => $html,
        ]);
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
                    $payment['hidden'] = @$response['found'] != true;
                    $payment['message'] = (!$payment['hidden']) ? '' : @$response['message'];
                    $payment['partscount'] = $this->checkoutService->isShowMonoPayParts();
                    break;
                case 'privatpayparts':
                    $payment['hidden'] = false;
                    $payment['message'] = '';
                    $payment['partscount'] = $this->checkoutService->isShowPrivatPayParts();
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

            $payment['total'] = $this->checkoutService->getTotal() + (int)$this->getDeliveryPrice();
            $payment['cur_partscount'] = ($cur_partscount) ?: ((isset($payment['payparts_count'])) ? $payment['payparts_count'] : $payment['partscount']);
            $payment['short_description'] = $payMethod->t('short_description');
        }

        return $payment;
    }

    //Возвращает json с html-блоками данных выбранного метода оплаты: для итоговых цен корзины.
    //Напр., для ОЧ выводим таблицу рассчета цены и select с частями + выбранный текущий. И т.д.
    //Далее можно использовать в js
    public function paymentForms(): JsonResponse
    {
        $payment_id = request('id');
        $cur_partscount = (int)request('payparts_count');

        $payment = $this->getPaymentData($payment_id, $cur_partscount);


        if ($payment_id && !empty($payment)) {
            $cart = 'checkout.partials.forms.payment_cart_' . $payment_id;
            $form = 'checkout.partials.forms.payment_details_' . $payment_id;
            return response()->json([
                'status' => 'success',
                /*'views' => [
                    'cart' => (view()->exists($cart)) ? view($cart, compact('payment'))->render() : '',
                    'form' => (view()->exists($form)) ? view($form, compact('payment'))->render() : '',
                ],*/
                'cart' => (view()->exists($cart)) ? view($cart, compact('payment'))->render() : '',
                'form' => (view()->exists($form)) ? view($form, compact('payment'))->render() : '',
                'html' => $this->getHtmlCheckoutBasket()
            ]);
        }

        return response()->json([
            'status' => 'error',
        ]);
    }

    public function monoVerifyPhone()
    {
        $form = session()->get('orderForm');
        $response = (isset($form['phone'])) ? (new MonoPayPartsService())->validatePhone($form['phone'])->getBody()->getContents() : '{}';
        return json_decode($response, true);
    }

    /**
     * @return array<string, string>
     */
    private function getOptions(mixed $options): array
    {
        $filters = [];

        if (count($options)) {
            foreach ($options as $characteristic => $option) {
                $characteristicModel = Characteristic::find($characteristic);
                $optionModel = CharacteristicOption::find($option);

                if ($characteristicModel && $optionModel) {
                    $filters[$characteristicModel->t('title')] = $optionModel->t('title');
                }
            }
        }

        return $filters;
    }

    public function remove(string $idProduct, Cart2 $cart): JsonResponse
    {
        //---- Analitic
        $this->product = $cart->get($idProduct)->model; //товар для аналитики
        $this->qty = (int)$cart->get($idProduct)->qty; //кол-во удаляемого товара
        $this->action = 'remove';
        //-------------

        $cart->remove($idProduct);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess('Товар успішно видалений з кошика', $cart);
    }

    public function update(string $idProduct, Cart2 $cart): JsonResponse
    {
        //$cart->update($idProduct, ['qty' => request('count')]);

        $count = (int)request('count');
        $this->product = $cart->get($idProduct)->model;

        // Аналитика
        $this->qty = $this->getNewCount($idProduct, $cart, $count); //для аналитики (только разница кол-ва)
        //

        //StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        //проверка наличия перед добавлением
        $availability = $this->basket->availableForOrder($this->product, $count, 'update');
        $cart->update(
            $idProduct,
            [
                'qty' => (($availability > 0) ?
                    $availability :
                    (($availability < 0) ? $count + $availability : $this->product->getQuantity()))
            ]
        );

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);
        if ($availability > 0) {
            return $this->getMessageJsonSuccess('Товар успішно оновлений', $cart);
        }

        //return $this->getMessageJsonSuccess('Товар успішно оновлений', $cart);
        return $this->getMessageJsonError(
            'Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество',
            $cart
        );
    }

    public function saveForm(): JsonResponse
    {
        //$form = request()->only('first_name', 'last_name', 'phone', 'email');
        $form = request()->except(['g-recaptcha-response', 'g_recaptcha_response', '_token']);
        session()->put('orderForm', $form);

        return response()->json([
            'status' => 'success',
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }

    public function getHtmlCheckoutBasket(): string
    {
        //Актуализируем состав корзины
        $this->basket->updateAvailabilityAndPrices();

        //$cartValues = $this->getCartValues();
        $cartValues = $this->checkoutService->getCartValues();

        return view('checkout.partials.products', $cartValues)->render();
    }

    public function getMessageJsonSuccess(string $message, Cart2 $cart): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                'message' => __t($message),
                'count' => $cart->count(),
                'total' => $cart->total(),
                'html' => $this->getHtmlCheckoutBasket(),
                'product' => $this->analitic(),
            ]
        );
    }

    private function getMessageJsonError(string $message, Cart2 $cart): JsonResponse
    {
        return response()->json(
            [
                'status' => 'false',
                'message' => __t($message),
                'count' => $cart->count(),
                'html' => $this->getHtmlCheckoutBasket()
            ]
        );
    }

    public function analiticData()
    {
        //$cartValues = $this->getCartValues();
        $cartValues = $this->checkoutService->getCartValues();

        $analytic = new Analytics($cartValues['productsInCart']->values());

        return response()->json(
            [
                'status' => 'success',
                'googleGA4' => [
                    'products' => json_decode($analytic->googleGA4product()),
                    true,
                    'total' => $cartValues['cartTotal']
                ]
            ]
        );
    }


    //Проверяем, изменился ли состав корзины с последнего запроса
    public function checkAvailabilityAndPrices(): JsonResponse
    {
        $hasChanges = $this->basket->updateAvailabilityAndPrices();

        return response()->json([
            'status' => 'success',
            'result' => $hasChanges,
            'html' => $this->getHtmlCheckoutBasket(),
            'message' => $hasChanges ? __t('Склад замовлення змінився. Перевірте будь ласка') : '',
            'count' => \Cart::instance('default')->count(),
            'total' => \Cart::instance('default')->total(),
        ]);
    }

    /**
     * Возвращает массив значений для аналитики
     * @return array
     */
    private function analitic()
    {
        return array_merge(
            $this->product->getProductDataAnalitic(),
            [
                'quantity' => $this->qty,
                'action' => $this->action
            ]
        );
    }

    /**
     * Возвращает модуль количества единиц измененого товара (только разницу между новым и старым значением)
     * Устанавливает тип действия для аналитики (уменьшено или увеличено значение)
     * @param string $idProduct
     * @param Cart $cart
     * @param $qty
     * @return float|int
     */
    private function getNewCount(string $idProduct, Cart2 $cart, $qty)
    {
        $cartItem = $cart->get($idProduct);
        $this->action = ($qty > $cartItem->qty) ? 'add' : 'remove';
        return abs($qty - $cartItem->qty);
    }

}
