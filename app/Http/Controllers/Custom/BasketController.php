<?php

namespace App\Http\Controllers\Custom;

use App\Events\QuickOrderCreate;
use App\Http\Controllers\BaseController;
use App\Http\Facades\LastModified;
use App\Jobs\StoreUnfinishedBasket;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\PayMethod;
use App\Models\Product;
use App\Services\Analytics;
use App\Services\Basket;
use App\Services\Checkout;
use App\Services\UnfinishedBasketService;
use App\Services\UtmLabel;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

class BasketController extends BaseController
{
    private UnfinishedBasketService $unfinishedBasketService;

    private $session;

    private $basketService;

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

    /**
     * Признак, что в корзине нужно выводить список платежных систем (напр., при оплате частями)
     * @var bool
     */
    private $has_payments = false;

    public function __construct(
        UnfinishedBasketService $unfinishedBasketService,
        SessionManager $session,
        Basket $basketService
    ) {
        $this->unfinishedBasketService = $unfinishedBasketService;
        $this->session = $session;
        $this->basketService = $basketService;
        $this->has_payments = request()->payments;
    }

    public function add(Product $product, Request $request, Cart $cart): JsonResponse
    {
        $options = (array)json_decode($request->get("options"));

        //---- Analitic
        $this->product = $product; //товар для аналитики
        $this->qty = (int)$request->get("count") ?: 1; //кол-во добавляемого товара
        $this->action = "add";
        (new Analytics())->setListName($product->id);
        //-------------

        //запоминаем выбранные методы оплаты, если есть
        $this->saveSelectedPayment($request);

        //проверка наличия перед добавлением
        if ($availability = $this->basketService->availableForOrder($product, $this->qty)) {
            $cart->add(
                $product->id,
                $product->title,
                $availability,
                $product->getPrice(),
                0,
                $options
            )->associate($product);

            StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

            return $this->getMessageJsonSuccess(
                "Товар успішно доданий до кошика",
                $cart
            );
        }

        return $this->getMessageJsonError(
            "Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество",
            $cart
        );
    }

    public function addMulti(Request $request, Cart $cart): JsonResponse
    {
        $ids = $request->get("ids");
        if (!$ids) {
            return $this->getMessageJsonError(
                "Что-то пошло не так. Попробуйте немного позже",
                $cart
            );
        }

        $ids = is_array($ids) ? $ids : explode(",", $ids);
        //$qty = (int)$request->get('count') ?: 1;

        //Analytic
        $this->action = "add";
        $this->qty = (int)$request->input("count", 1);
        (new Analytics())->setMultiListName($ids);
        //--

        $products = Product::whereIn("id", $ids)->active()->where("quantity", ">", 0)->get();
        if ($products->isEmpty()) {
            return $this->getMessageJsonError(
                "Выбранные товары пока недоступны или их больше нет в наличии. Попробуйте позже.",
                $cart
            );
        }

        $productsData = [];

        foreach ($products as $product) {
            if ($availability = $this->basketService->availableForOrder($product, $this->qty)) {
                $cart->add(
                    $product->id,
                    $product->title,
                    $availability,
                    $product->getPrice(),
                    0,
                    []
                )->associate($product);

                $productsData[] = $product;
            }
        }

        if (empty($productsData)) {
            return $this->getMessageJsonError(
                "Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество",
                $cart
            );
        }

        return $this->getMessageJsonSuccess(
            "Товары успешно добавлены в корзину",
            $cart,
            $productsData
        );
    }

    public function remove(string $idProduct, Cart $cart): JsonResponse
    {
        //---- Analitic
        $this->product = $cart->get($idProduct)->model; //товар для аналитики
        $this->qty = (int)$cart->get($idProduct)->qty; //кол-во удаляемого товара
        $this->action = "remove";
        //-------------

        $cart->remove($idProduct);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess(
            "Товар успішно видалений з кошика",
            $cart
        );
    }

    public function update(string $idProduct, Cart $cart): JsonResponse
    {
        $count = (int)request("count");
        $this->product = $cart->get($idProduct)->model; //получаем товар

        // Аналитика
        $this->qty = $this->getNewCount($idProduct, $cart, $count); //для аналитики (только разница кол-ва)
        (new Analytics())->setListName($this->product->id);
        //

        //проверка наличия перед добавлением
        $availability = $this->basketService->availableForOrder($this->product, $count, "update");

        $cart->update($idProduct, [
            "qty" =>
                $availability > 0
                    ? $availability
                    : ($availability < 0 ? $count + $availability : $this->product->getQuantity()),
        ]);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);
        if ($availability > 0) {
            return $this->getMessageJsonSuccess(
                "Товар успішно оновлений",
                $cart
            );
        }

        return $this->getMessageJsonError(
            "Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество",
            $cart
        );
    }

    public function buyOneClick(Product $product, Request $request, UtmLabel $utmLabel): JsonResponse
    {
        $order = Order::create([
            "user_id" => app("user")->id ?? null,
            "first_name" => app("user")->first_name ?? "",
            "last_name" => app("user")->last_name ?? "",
            "phone" => $request->get("phone"),
            "cost" => $product->getPrice(),
            "is_quick" => 1,
            "order_status_id" => 1,
            "pay_method_id" => 1
        ]);

        OrderProducts::create([
            "order_id" => $order->id,
            "product_id" => $product->id,
            "count" => 1,
            "price" => $product->getPrice(),
            "base_price" => $product->getPrice(),
        ]);
        QuickOrderCreate::dispatch($order); //Здесь в Listeners/Order/SendToGA4.php передаем для GA4

        $this->sendAnalytic($order);

        //return $this->returnSuccess('Дякую. Скоро з Вами зв\'яжуться');  //Всплывающее сообщение

        //Вместо всплывающего - редирект на страницу Спасибо за заказ
        session()->put(["order" => $order]);
        session()->save();
        //return redirect()->route('checkout.complete');
        return response()->json([
            "status" => "success",
            "message" => __t('Дякую. Скоро з Вами зв\'яжуться'),
            "url" => url("checkout/complete"),
        ]);
    }

    private function sendAnalytic(Order $order): View
    {
        $items = $order->cartOrderProducts;

        $analytic = new Analytics($items);

        $universalAnalytics = $analytic->universal();
        $googleAnalytics = $analytic->google();
        //$googleGA4Analytics = $analytic->googleGA4(); Для GA4 передается в Listeners/Order/SendToGA4.php

        return view(
            "partials.internet_marketing",
            compact(
                "order",
                "universalAnalytics",
                "googleAnalytics" /*, 'googleGA4Analytics'*/
            )
        );
    }

    private function getHtmlPopupBasket(): string
    {
        $productsInCart = \Cart::content()->reverse();
        $cartTotal = \Cart::total();
        //return view('partials.cart', compact('productsInCart', 'cartTotal'))->render();

        //--
        $view = $this->has_payments ? "basket.partials.cart_payparts" : "basket.partials.cart";
        $paymentslist = $this->has_payments ? $this->buildPaymentsList() : [];
        return view($view, compact("productsInCart", "cartTotal"), $paymentslist)->render();
        //--
    }

    /*private function getMessageJsonSuccess(string $message, Cart $cart): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                'message' => __t($message),
                'count' => $cart->count(),
                'html' => $this->getHtmlPopupBasket(),
                'product' => $this->analitic(),
            ]
        );
    }*/
    private function getMessageJsonSuccess(string $message, Cart $cart, array $productsData = []): JsonResponse
    {
        $response = [
            "status" => "success",
            "message" => __t($message),
            "count" => $cart->count(),
            "action" => $this->action,
            "html" => $this->getHtmlPopupBasket(),
        ];

        if (!empty($productsData)) {
            foreach ($productsData as $product) {
                $response["products"][] = $product->getProductDataAnalitic() + [
                        "quantity" => $this->qty,
                    ];
            }
        } else {
            $response["products"] = [
                $this->product->getProductDataAnalitic() + [
                    "quantity" => $this->qty,
                ],
            ];
        }

        return response()->json($response);
    }

    private function getMessageJsonError(string $message, Cart $cart): JsonResponse
    {
        return response()->json([
            "status" => "false",
            "message" => __t($message),
            "count" => $cart->count(),
            "html" => $this->getHtmlPopupBasket(),
        ]);
    }

    public function getCart(): JsonResponse
    {
        if (request()->ajax()) {
            return response()->json([
                "status" => "success",
                "html" => $this->getHtmlPopupBasket(),
            ]);
        }
    }

    /**
     * Возвращает модуль количества единиц измененного товара (только разницу между новым и старым значением)
     * Устанавливает тип действия для аналитики (уменьшено или увеличено значение)
     * @param string $idProduct
     * @param Cart $cart
     * @param $qty
     * @return float|int
     */
    private function getNewCount(string $idProduct, Cart $cart, $qty): float|int
    {
        $cartItem = $cart->get($idProduct);
        $this->action = $qty > $cartItem->qty ? "add" : "remove";
        return abs($qty - $cartItem->qty);
    }

    /**
     * Возвращает массив значений для аналитики
     * @return array
     */
    private function analitic(): array
    {
        return array_merge($this->product->getProductDataAnalitic(), [
            "quantity" => $this->qty,
            "action" => $this->action,
        ]);
    }

    //Запоминаем выбор метода оплаты и кол-ва платежей
    private function saveSelectedPayment(Request $request)
    {
        if ($this->has_payments) {
            $this->session->put("payparts", [
                "pay_method_id" => $request->get("payment_id"),
                "payparts_count" => $request->get("payparts_count"),
                "product_id" => $request->get("product_id"),
            ]);
        }
    }

    //Блок с просчетом цен для выбранного метода оплаты частями для всей корзины и запоминанием выбора
    /*public function paypartsPrices()
    {
        $payment_id = request("id");
        $cur_partscount = (int)request("payparts_count");
        if ($payment_id) {
            $payMethod = PayMethod::with("checkout")
                ->where("id", $payment_id)
                ->first();
            $payMethodName = $payMethod->checkout->slug ?? "";

            switch ($payMethodName) {
                case "monopayparts":
                    $payment["partscount"] = $this->checkoutService->isShowMonoPayParts();
                    break;
                case "privatpayparts":
                    $payment["partscount"] = $this->checkoutService->isShowPrivatPayParts();
                    break;
                default:
                    $payment["partscount"] = 0;
                    break;
            }

            $payment["total"] = \Cart::total();
            $payment["cur_partscount"] = $cur_partscount
                ? $cur_partscount
                : $payment["partscount"];

            //Запоминаем выбор метода оплаты и кол-ва платежей
            $this->session->put("payparts", [
                "pay_method_id" => $payment_id,
                "payparts_count" => $cur_partscount,
            ]);

            $cart = "basket.partials.forms.payment_cart_" . $payment_id;
            return response()->json([
                "status" => "success",
                "views" => [
                    "cart" => view()->exists($cart)
                        ? view($cart, compact("payment"))->render()
                        : "",
                ],
            ]);
        }
        return response()->json([
            "status" => "error",
        ]);
    }

    //Построение списка платежных систем Оплата частями для корзины целиком
    private function buildPaymentsList()
    {
        //Берем из сессии метод оплаты и кол-во частей
        $payparts = $this->session->get("payparts");

        //Берем методы оплаты только Оплата частями
        $payMethods = PayMethod::whereHas("checkout", function ($query) {
            return $query->whereIn("slug", ["monopayparts", "privatpayparts"]);
        })
            ->active()
            ->orderPriority()
            ->get();

        $payMethodsResult = [];

        foreach ($payMethods as $payMethod) {
            switch ($payMethod->checkout->slug) {
                case "monopayparts":
                    $partscount = $this->checkoutService->isShowMonoPayParts();
                    break;
                case "privatpayparts":
                    $partscount = $this->checkoutService->isShowPrivatPayParts();
                    break;
                default:
                    $partscount = 0;
                    break;
            }

            if (!$partscount) {
                continue;
            }
            $payMethodsResult[] = [
                "name" => $payMethod->t("title"),
                "id" => $payMethod->id,
                "picture" => $payMethod->picture,
                "slug" => $payMethod->checkout->slug,
                "partscount" => $partscount,
            ];
        }
        return [
            "payparts" => $payparts,
            "payments" => collect($payMethodsResult),
        ];
    }*/

    //Вывод данных про ОЧ для конкретного товара - попап
    public function viewPaypartsForProduct(
        string $idProduct,
        Cart $cart
    ): JsonResponse {
        $payments = [];

        //запоминаем выбранные методы оплаты, если есть
        $this->saveSelectedPayment(request());

        if ($product = Product::find($idProduct)) {
            $payments = $this->buildPaymentsListForProduct($product);
            $product->options = [];
        }

        $response = [
            "status" => !$product ? "error" : "success",
            //"count" => 1,
            "message" => !$product
                ? __t("Извините, в данный момент товар недоступен.")
                : "",
            "html" => view(
                "basket.partials.cart_payparts",
                compact("product"),
                $payments
            )->render(),
        ];

        return response()->json($response);
    }

    //Блок с просчетом цен для выбранного метода оплаты частями для конкретного товара и запоминанием выбора
    public function viewPaypartsPricesForProduct(Product $product)
    {
        $payment_id = request("id");
        $cur_partscount = (int)request("payparts_count");
        if ($payment_id) {
            $payMethod = PayMethod::with("checkout")
                ->where("id", $payment_id)
                ->first();
            $payMethodName = $payMethod->checkout->slug ?? "";

            switch ($payMethodName) {
                case "monopayparts":
                    $payment["partscount"] =
                        $product->has_mono_payparts == 1
                            ? $product->mono_payparts_count
                            : false;
                    break;
                case "privatpayparts":
                    $payment["partscount"] =
                        $product->has_privat_payparts == 1
                            ? $product->privat_payparts_count
                            : false;
                    break;
                default:
                    $payment["partscount"] = 0;
                    break;
            }

            $payment["total"] = $product->getPrice();
            $payment["cur_partscount"] = $cur_partscount
                ? $cur_partscount
                : $payment["partscount"];

            //Запоминаем выбор метода оплаты и кол-ва платежей
            $this->session->put("payparts", [
                "pay_method_id" => $payment_id,
                "payparts_count" => $cur_partscount,
                "product_id" => $product->id,
            ]);

            $cart = "basket.partials.forms.payment_cart_" . $payment_id;
            return response()->json([
                "status" => "success",
                "views" => [
                    "cart" => view()->exists($cart)
                        ? view($cart, compact("payment"))->render()
                        : "",
                ],
            ]);
        }
        return response()->json([
            "status" => "error",
        ]);
    }

    //Список методов оплаты Оплата частями для конкретного товара
    private function buildPaymentsListForProduct(Product $product)
    {
        //Берем из сессии метод оплаты и кол-во частей
        $payparts = $this->session->get("payparts");

        //Берем методы оплаты только Оплата частями
        $payMethods = PayMethod::whereHas("checkout", function ($query) {
            return $query->whereIn("slug", ["monopayparts", "privatpayparts"]);
        })
            ->active()
            ->orderPriority()
            ->get();

        $payMethodsResult = [];

        foreach ($payMethods as $payMethod) {
            switch ($payMethod->checkout->slug) {
                case "monopayparts":
                    $partscount =
                        $product->has_mono_payparts == 1
                            ? $product->mono_payparts_count
                            : false;
                    break;
                case "privatpayparts":
                    $partscount =
                        $product->has_privat_payparts == 1
                            ? $product->privat_payparts_count
                            : false;
                    break;
                default:
                    $partscount = 0;
                    break;
            }

            if (!$partscount) {
                continue;
            }
            $payMethodsResult[] = [
                "name" => $payMethod->t("title"),
                "id" => $payMethod->id,
                "picture" => $payMethod->picture,
                "slug" => $payMethod->checkout->slug,
                "partscount" => $partscount,
            ];
        }
        return [
            "payparts" => $payparts,
            "payments" => collect($payMethodsResult),
        ];
    }
}
