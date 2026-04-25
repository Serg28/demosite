<?php

namespace App\Http\Controllers;

use App\Events\QuickOrderCreate;
use App\Jobs\StoreUnfinishedBasket;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\PayMethod;
use App\Models\Product;
use App\Services\Analytics;
use App\Services\Basket;
use App\Services\UnfinishedBasketService;
use App\Services\UtmLabel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BasketController extends BaseController
{
    private UnfinishedBasketService $unfinishedBasketService;

    private Basket $basketService;

    /**
     * Кол-во удаленного/измененого/добавленого товара для аналитики
     * @var int
     */
    private int $qty;

    private $product;

    /**
     * Название действия для аналитики
     * @var string
     */
    private string $action;

    /**
     * Признак, что в корзине нужно выводить список платежных систем (напр., при оплате частями)
     * @var bool
     */
    private mixed $has_payments = false;

    public function __construct(
        UnfinishedBasketService $unfinishedBasketService,
        Basket $basketService
    ) {
        $this->unfinishedBasketService = $unfinishedBasketService;
        $this->basketService = $basketService;
        $this->has_payments = request()->payments;
    }

    /**
     * Добавление товара в корзину по ID.
     *
     * @param int $productId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCartById(int $productId, Request $request)
    {
        // Получаем продукт по ID
        $product = Product::find($productId);

        if (!$product || !$product->isActive()) {
            return $this->returnError('Товар не знайдений або недоступний');
        }

        // Опции могут быть получены из запроса, если есть
        $jsonOptions = $request->input('options', null);

        // Получаем количество из запроса или используем значение по умолчанию
        $quantity = $request->input('quantity', 1);

        // Получаем количество из запроса или используем значение по умолчанию
        $price = $request->input('p', $product->getPrice());

        // Используем сервис корзины для добавления товара
        return $this->basketService->add($product, $product->title, $quantity, $price, 0, $jsonOptions);

    }

    public function add(Product $product, Request $request): JsonResponse
    {
        $options = (array)json_decode($request->get("options"));
        $options['qtyOld'] = null;

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
            \Cart::add(
                    $product->id,
                    $product->title,
                    $availability,
                    $product->getPrice(),
                    0,
                    $options
                )
                ->associate($product);

            StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

            return $this->getMessageJsonSuccess("Товар успішно доданий до кошика");
        }

        return $this->getMessageJsonError("Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество");
    }

    public function addMulti(Request $request): JsonResponse
    {
        $ids = $request->get("ids");
        if (!$ids) {
            return $this->getMessageJsonError("Что-то пошло не так. Попробуйте немного позже");
        }

        $ids = is_array($ids) ? $ids : explode(",", $ids);

        //Analytic
        $this->action = "add";
        $this->qty = (int)$request->input("count", 1);
        (new Analytics())->setMultiListName($ids);
        //--

        $products = Product::whereIn("id", $ids)->active()->available()->get();
        if ($products->isEmpty()) {
            return $this->getMessageJsonError("Выбранные товары пока недоступны или их больше нет в наличии. Попробуйте позже.");
        }

        $productsData = [];

        foreach ($products as $product) {
            if ($availability = $this->basketService->availableForOrder($product, $this->qty)) {
                \Cart::add($product->id, $product->title, $availability, $product->getPrice(), 0, [])->associate($product);

                $productsData[] = $product;
            }
        }

        if (empty($productsData)) {
            return $this->getMessageJsonError( "Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество");
        }

        return $this->getMessageJsonSuccess("Товары успешно добавлены в корзину", $productsData);
    }

    public function remove(string $idProduct): JsonResponse
    {
        //---- Analytic
        $this->product = \Cart::get($idProduct)->model; //товар для аналитики
        $this->qty = (int)\Cart::get($idProduct)->qty; //кол-во удаляемого товара
        $this->action = "remove";
        //-------------

        \Cart::remove($idProduct);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess("Товар успішно видалений з кошика");
    }

    public function update(string $idProduct): JsonResponse
    {
        $count = (int)request("count");
        $this->product = \Cart::get($idProduct)->model; //получаем товар

        // Аналитика
        $this->qty = $this->getNewCount($idProduct, $count); //для аналитики (только разница кол-ва)
        (new Analytics())->setListName($this->product->id);
        //

        //проверка наличия перед добавлением
        $availability = $this->basketService->availableForOrder($this->product, $count, "update");
        \Cart::update($idProduct, [
            "qty" =>
                $availability > 0
                    ? $availability
                    : ($availability < 0 ? $count + $availability : $this->product->getQuantity()),
            "options" => ['qtyOld' => null]
        ]);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);
        if ($availability > 0) {
            return $this->getMessageJsonSuccess("Товар успішно оновлений");
        }

        return $this->getMessageJsonError("Товара в нужном количестве нет в наличии. Добавлено максимально возможное количество");
    }

    public function buyOneClick(Product $product, Request $request, UtmLabel $utmLabel): JsonResponse {
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
            "base_amount" => $product->getPrice(),
            "total_amount" => $product->getPrice(),
        ]);
        QuickOrderCreate::dispatch($order); //Здесь в Listeners/Order/SendToGA4.php передаем для GA4

        $this->sendAnalytic($order);

        //return $this->returnSuccess('Дякую. Скоро з Вами зв\'яжуться');  //Всплывающее сообщени

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
        //Актуализируем состав корзины
        $this->basketService->updateAvailabilityAndPrices();

        $productsInCart = \Cart::content()->reverse();
        $cartTotal = \Cart::total();
        //return view('partials.cart', compact('productsInCart', 'cartTotal'))->render();

        //--
        $view = $this->has_payments
            ? "basket.partials.cart_payparts"
            : "partials.cart";
        $paymentslist = $this->has_payments ? $this->buildPaymentsList() : [];
        return view(
            $view,
            compact("productsInCart", "cartTotal"),
            $paymentslist
        )->render();
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
    private function getMessageJsonSuccess(string $message, array $productsData = []): JsonResponse
    {
        $response = [
            "status" => "success",
            "message" => __t($message),
            "count" => \Cart::count(),
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

    private function getMessageJsonError(string $message): JsonResponse
    {
        return response()->json([
            "status" => "false",
            "message" => __t($message),
            "count" => \Cart::count(),
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
     * Возвращает модуль количества единиц измененого товара (только разницу между новым и старым значением)
     * Устанавливает тип действия для аналитики (уменьшено или увеличено значение)
     * @param string $idProduct
     * @param $qty
     * @return float|int
     */
    private function getNewCount(string $idProduct, $qty): float|int
    {
        $cartItem = \Cart::get($idProduct);
        $this->action = $qty > $cartItem->qty ? "add" : "remove";
        return abs($qty - $cartItem->qty);
    }

    /**
     * Возвращает массив значений для аналитики
     * @return array
     */
    private function analitic()
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
            session()->put("payparts", [
                "pay_method_id" => $request->get("payment_id"),
                "payparts_count" => $request->get("payparts_count"),
                //'product_id' => $this->product->id
                "product_id" => $request->get("product_id"),
            ]);
        }
    }

    //Вывод данных про ОЧ для конкретного товара - попап
    public function viewPaypartsForProduct(string $idProduct): JsonResponse
    {
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
            "message" => !$product ? __t("Извините, в данный момент товар недоступен.") : "",
            "html" => view(
                "basket.partials.cart_payparts",
                compact("product"),
                $payments
            )->render(),
        ];

        return response()->json($response);
    }

    //Блок с просчетом цен для выбранного метода оплаты частями для конкретного товара и запоминанием выбора
    public function viewPaypartsPricesForProduct(Product $product): JsonResponse
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
                    $payment["partscount"] = $product->getMonoPartsCount();
                    break;
                case "privatpayparts":
                    $payment["partscount"] = $product->getPrivatPartsCount();
                    break;
                default:
                    $payment["partscount"] = 0;
                    break;
            }

            $payment["total"] = $product->getPrice();
            $payment["cur_partscount"] = $cur_partscount ?: $payment["partscount"];

            //Запоминаем выбор метода оплаты и кол-ва платежей
            session()->put("payparts", [
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
    private function buildPaymentsListForProduct(Product $product): array
    {
        //Берем из сессии метод оплаты и кол-во частей
        $selectedPayParts = session()->get("payparts");

        //Берем методы оплаты только Оплата частями
        $payMethods = PayMethod::whereHas("checkout", function ($query) {
            return $query->whereIn("slug", ["monopayparts", "privatpayparts"]);
        })->active()->orderPriority()->get();

        $payMethodsResult = [];

        foreach ($payMethods as $payMethod) {
            switch ($payMethod->checkout->slug) {
                case "monopayparts":
                    $availablePartsCount = $product->getMonoPartsCount();
                    break;
                case "privatpayparts":
                    $availablePartsCount = $product->getPrivatPartsCount();
                    break;
                default:
                    $availablePartsCount = 0;
                    break;
            }

            if (!$availablePartsCount) {
                continue;
            }
            $payMethodsResult[] = [
                "name" => $payMethod->t("title"),
                "id" => $payMethod->id,
                "picture" => $payMethod->picture,
                "slug" => $payMethod->checkout->slug,
                "partscount" => $availablePartsCount,
            ];
        }
        return [
            "payparts" => $selectedPayParts,
            "payments" => collect($payMethodsResult),
        ];
    }
}
