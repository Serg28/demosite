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
use App\Models\Product;
use App\Models\PromoCode;
use App\Services\Analytics;
use App\Services\DiscountCard;
use App\Services\Promocodes;
use App\Services\UnfinishedBasketService;
use App\Services\UtmLabel;
use Gloudemans\Shoppingcart\Cart as Cart2;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class CheckoutController_orig extends Controller
{
    private UnfinishedBasketService $unfinishedBasketService;

    /**
     * Instance of the session manager.
     *
     * @var \Illuminate\Session\SessionManager
     */
    private $session;

    private $promocode;

    private $discountcard;

    private $userfields = ['email', 'phone', 'first_name', 'last_name'];

    public function __construct(
        UnfinishedBasketService $unfinishedBasketService,
        SessionManager $session,
        Promocodes $promocode,
        DiscountCard $discountcard
    ) {
        $this->unfinishedBasketService = $unfinishedBasketService;
        $this->session = $session;

        $this->promocode = $promocode;
        $this->discountcard = $discountcard;
    }

    public function index(): View|RedirectResponse
    {
        //Если нужно сохранять данные о доставке после перезагрузке страницы, закомментировать
        $this->resetDelivery();

        $cartValues = $this->getCartValues();

        if (! $cartValues['countProducts']) {
            return redirect('/');
        }

        return view('checkout.index', $cartValues);
    }

    private function getCartValues(): array
    {
        $cartSubTotal = $this->getRawSubtotal(); //Возвращает чистый подитог цены всех позиций без скидок
        $promo = $this->promocode->get();
        $delivery = $this->getDelivery();
        $cartDeliveryPrice = ($delivery) ? $delivery->price : 0;
        $cartDeliveryDesc = ($delivery) ? strip_tags($delivery->t('description')) : '';
        $promoSale = (($promo) ? $promo->sale : '');
        $promoCode = (($promo) ? $promo->code : '');
        //$promoSaleSum = ($promo) ? (int)(($cartSubTotal * $promoSale) / 100) : 0;
        $promoSaleSum = ($promo) ? round(($cartSubTotal * $promoSale) / 100) : 0;
        $discountSale = $this->discountcard->get(); //Размер % скидки по дисконтной карте
        //$discountSaleSum = ($discountSale) ? (int)(($cartSubTotal * $discountSale) / 100) : 0;
        $discountSaleSum = ($discountSale) ? round(($cartSubTotal * $discountSale) / 100) : 0;
        $cartTotal = ($cartSubTotal - $promoSaleSum - $discountSaleSum) + $cartDeliveryPrice;
        $user = (app('user') ?
            collect(app('user')->only($this->userfields)) :
            collect())->toArray();
        $dataForm = $this->session->get('orderForm');
        $user = ($dataForm) ?
            array_merge(
                $user,
                array_diff(collect($dataForm)->only($this->userfields)->toArray(), [''])
            ) :
            $user;
        //$this->discountcard->getSaleSum();
        return [
            'countProducts' => Cart::count(),
            'productsInCart' => Cart::content(),
            'payMethods' => $this->preparePayMethods(),
            'cartTotal' => $cartTotal,
            'cartSubTotal' => $cartSubTotal,
            'promo' => $promo,
            'promoCode' => $promoCode,
            'promoSale' => $promoSale,
            'promoSaleSum' => $promoSaleSum,
            'discountSale' => $discountSale,
            'discountSaleSum' => $discountSaleSum,
            'cartDeliveryPrice' => $cartDeliveryPrice,
            'cartDeliveryDesc' => $cartDeliveryDesc,
            'delivery' => $delivery,
            /*'user' => (app('user') ?
                collect(app('user')->only(['email', 'phone', 'first_name', 'last_name'])) :
                collect()),
            'userArray' => (app('user') ?
                collect(app('user')->only(['email', 'phone', 'first_name', 'last_name'])) :
                collect())->toArray(),*/
            'user' => $user,
            'isFbq' => (setting('checkbox_facebook_pixel') ?: 0),
        ];
    }

    //Возвращает чистый подитог цены всех позиций без скидок
    private function getRawSubtotal()
    {
        return Cart::content()->sum(function ($item) {
            return $item->price * $item->qty;
        });
    }

    public function getCart()
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
        $order = session('order');

        if (! $order) {
            return redirect('/');
        }

        $items = $order->cartOrderProducts;

        $analytic = new Analytics($items);

        $universalAnalytics = $analytic->universal();

        $googleAnalytics = $analytic->google();

        $dynamicAnalytics = $analytic->dynamic();

        return view('checkout.complete', compact('order', 'universalAnalytics', 'googleAnalytics', 'dynamicAnalytics'));
    }

    public function send(OrderRequest $request, UtmLabel $utmLabel): RedirectResponse
    {
        $data = $request->except(['g-recaptcha-response', 'g_recaptcha_response', '_token']);

        $order = Order::create($data)->afterSave();

        Cart::destroy();

        app(UnfinishedBasketService::class)->delete();

        $request->session()->flash('order', $order);

        OrderCreate::dispatch($order);

        if ($order->payMethod->checkout) {
            return redirect()->to($order->urlPayment());
        }

        return redirect()->route('checkout.complete');
    }

    private function getDelivery()
    {
        return $this->session->get('delivery');
    }

    private function setDelivery(int $delivery_id)
    {
        if (! $delivery_id) {
            return $this->resetDelivery();
        }

        //получение данных о доставке
        $delivery = Delivery::where('id', '=', $delivery_id)->active()->first();

        return $this->session->put('delivery', $delivery);
    }

    private function resetDelivery()
    {
        return $this->session->remove('delivery');
    }

    public function removeDelivery()
    {
        $this->resetDelivery();

        return response()->json([
            'status' => 'success',
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }

    public function checkPromoCode(CheckPromoCodeRequest $request): JsonResponse
    {
        $code = $this->promocode->check($request->get('code'));

        //-- Проверка условий промокода
        //1. Применять ли вместе с дисконтными картами
        /*$discount = $this->discountcard->get();
        if ($code) {
            if (!$code->use_for_discount_cards && $discount) {
                $code = null;
                $this->promocode->resetPromoCode();
            }
        }*/
        //--

        if ($code) {
            return response()->json([
                'status' => 'success',
                'sale' => $code->sale,
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
        if ($this->promocode->remove()) {
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

        $cities = City::where('title->ua', 'like', $query.'%')
            ->orWhere('title->ru', 'like', $query.'%')
            ->orWhere('title->en', 'like', $query.'%')
            ->orderBy('title->'.App::getLocale(), 'asc')->get();

        return $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'title' => $city->t('origin'),
            ];
        });
    }

    public function getDeliveries(): array
    {
        $city = City::find(request('city'));

        $defaultDeliveries = Delivery::where('is_show_for_all_cities', 1)->active()->orderBy('priority')->get();

        $deliveries = $city->deliveries()->active()->get()->merge($defaultDeliveries);

        $deliveryTerm = $this->getDeliveryTerm();

        $deliveriesResultDefault[] = [
            'id' => null,
            'title' => __t('Доставка'),
            'price' => 0,
            'description' => '',
            'type' => null,
        ];

        $deliveriesResult = $deliveries->sortBy('priority')->map(function ($delivery) use ($deliveryTerm) {
            return [
                'id' => $delivery->id,
                'title' => $delivery->t('title'),
                'price' => $delivery->price,
                'description' => $delivery->t('description'),
                'type' => $delivery->type,
                'term' => $deliveryTerm,
            ];
        })->toArray();

        //return array_merge($deliveriesResultDefault, $deliveriesResult);
        return $deliveriesResult;
    }

    //Срок отправки. Если все товары готовы к отправке - Сегодня. Иначе - по сроку поступления
    public function getDeliveryTerm()
    {
        $ids = $this->getIdProductsIncart();
        if (! $ids) {
            return '';
        }

        $products = Product::whereIn('id', $ids)->where('product_status_id', '!=',
            1)->active()->orderBy('delivery_days', 'desc')->first();

        return (! $products) ? __t('Завтра') : __t('Отправка через').' '.preg_replace('/[^-,.0-9]/', '',
            $products->t('delivery_days')).' '.__t('дня');
    }

    private function getIdProductsIncart()
    {
        return Cart::content()->map(function ($item, $key) {
            return $item->id;
        })->toArray();
    }

    //Custom. Общая точка для всех методов доставки
    public function getDeliveryPointers(): JsonResponse
    {
        $type = request('type');
        $id = request('id');
        $price = request('price');
        $points = collect();

        //Устанавливаем данные о выбранной доставке. Для пересчета цены доставки в чекауте
        $this->setDelivery($id);

        switch ($type) {
            case 'pickup':
                $points = $this->getDeliveryPickupPointers();
                break;
            default:
                $points = $this->getDeliveryNPPointers();
                break;
        }

        $html = ($id) ? view('checkout.partials.forms.delivery_'.$id,
            compact('points'))->render() : __t('Для этого метода доставки нет отделений');

        return response()->json([
            'status' => 'success',
            'html' => $html,
        ]);
    }

    public function getDeliveryPickupPointers(): Collection
    {
        $points = DeliveryPickupPoint::orderBy('priority', 'asc')->get();

        return $points->map(function ($point) {
            return [
                'id' => $point->id,
                'title' => $point->t('address'),
                'text' => '',
            ];
        });
    }

    public function getDeliveryNPPointers(): Collection
    {
        $city = City::find(request('city'));

        return $this->getWarehouses(request('type'), $city);
    }

    public function getWarehouses(string $type, City $city): Collection
    {
        switch ($type) {
            case 'ukrposhta':
                $getDepartments = $city->ukrposhta();
                break;
            case 'justin':
                $getDepartments = $city->justin();
                break;
            case 'meest':
                $getDepartments = $city->meest();
                break;
            default:
                $getDepartments = $city->departments();
        }

        $departments = $getDepartments->orderBy('title->'.App::getLocale(), 'asc')->get();

        return $departments->map(function ($department) {
            return [
                'id' => $department->id,
                'title' => $department->t('title'),
            ];
        });
    }

    private function preparePayMethods(int $delivery_id = 0): Collection
    {
        if ($delivery_id) {
            $payMethods = PayMethod::whereHas('deliveries', function ($query) use ($delivery_id) {
                return $query->where('delivery_id', $delivery_id);
            })->active()->orderPriority()->get();
        } else {
            $payMethods = PayMethod::active()->orderPriority()->get();
        }
        $payMethodsResult = [];
        /*$payMethodsResult[] = [
            'name' => __t('Оплата'),
            'id' => null
        ];*/

        foreach ($payMethods as $payMethod) {
            $payMethodsResult[] = [
                'name' => $payMethod->t('title'),
                'id' => $payMethod->id,
            ];
        }

        return collect($payMethodsResult);
    }

    private function prepareProducts(): Collection
    {
        $productsCart = Cart::content();
        $products = [];

        foreach ($productsCart as $product) {
            $products[] = [
                'id' => $product->rowId,
                'img_link' => $product->model->getUrl(),
                'img_src' => $product->model->getImgPath(115, 115),
                'title' => $product->model->t('title'),
                'price' => $product->price,
                'count' => $product->qty,
                'total' => $product->price * $product->qty,
                'product_id' => $product->id,
                'options' => $this->getOptions($product->options),
            ];
        }

        return collect($products);
    }

    public function getPayments()
    {
        $payments = $this->preparePayMethods(request('id'))->toArray();
        $html = ($payments) ? view('checkout.partials.forms.payment',
            compact('payments'))->render() : __t('Для этого метода доставки нет методов оплаты');

        return response()->json([
            'status' => 'success',
            'html' => $html,
        ]);
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
        $cart->remove($idProduct);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess('Товар успішно видалений з кошика', $cart);
    }

    public function update(string $idProduct, Cart2 $cart): JsonResponse
    {
        $cart->update($idProduct, ['qty' => request('count')]);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess('Товар успішно оновлений', $cart);
    }

    public function saveForm()
    {
        //$form = request()->only('first_name', 'last_name', 'phone', 'email');
        $form = request()->except(['g-recaptcha-response', 'g_recaptcha_response', '_token']);
        $this->session->put('orderForm', $form);

        return response()->json([
            'status' => 'success',
            'html' => $this->getHtmlCheckoutBasket(),
        ]);
    }

    public function getHtmlCheckoutBasket(): string
    {
        $cartValues = $this->getCartValues();

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
            ]
        );
    }
}
