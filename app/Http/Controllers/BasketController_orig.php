<?php

namespace App\Http\Controllers\Custom;

use App\Events\QuickOrderCreate;
use App\Http\Controllers\BaseController;
use App\Jobs\StoreUnfinishedBasket;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Services\Analytics;
use App\Services\UnfinishedBasketService;
use App\Services\UtmLabel;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasketController_orig extends BaseController
{
    private UnfinishedBasketService $unfinishedBasketService;

    public function __construct(UnfinishedBasketService $unfinishedBasketService)
    {
        $this->unfinishedBasketService = $unfinishedBasketService;
    }

    public function add(Product $product, Request $request, Cart $cart): JsonResponse
    {
        $options = (array) json_decode($request->get('options'));

        $cart->add(
            $product->id,
            $product->title,
            $request->get('count') ?: 1,
            $product->getPrice(),
            0,
            $options
        )->associate($product);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess('Товар успішно доданий до кошика', $cart);
    }

    public function remove(string $idProduct, Cart $cart): JsonResponse
    {
        $cart->remove($idProduct);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess('Товар успішно видалений з кошика', $cart);
    }

    public function update(string $idProduct, Cart $cart): JsonResponse
    {
        $cart->update($idProduct, ['qty' => request('count')]);

        StoreUnfinishedBasket::dispatch($this->unfinishedBasketService);

        return $this->getMessageJsonSuccess('Товар успішно оновлений', $cart);
    }

    public function buyOneClick(Product $product, Request $request, UtmLabel $utmLabel): JsonResponse
    {
        $order = Order::create([
            'user_id' => app('user')->id ?? null,
            'first_name' => app('user')->first_name ?? '',
            'last_name' => app('user')->last_name ?? '',
            'phone' => $request->get('phone'),
            'cost' => $product->getPrice(),
            'is_quick' => 1,
        ]);

        OrderProducts::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'count' => 1,
            'price' => $product->getPrice(),
        ]);
        QuickOrderCreate::dispatch($order);

        $this->sendAnalytic($order);

        return $this->returnSuccess('Дякую. Скоро з Вами зв\'яжуться');
    }

    private function sendAnalytic(Order $order): View
    {
        $items = $order->cartOrderProducts;

        $analytic = new Analytics($items);

        $universalAnalytics = $analytic->universal();
        $googleAnalytics = $analytic->google();

        return view(
            'partials.internet_marketing',
            compact('order', 'universalAnalytics', 'googleAnalytics')
        );
    }

    private function getHtmlPopupBasket(): string
    {
        $productsInCart = \Cart::content();
        $cartTotal = \Cart::total();

        return view('partials.cart', compact('productsInCart', 'cartTotal'))->render();
    }

    private function getMessageJsonSuccess(string $message, Cart $cart): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                'message' => __t($message),
                'count' => $cart->count(),
                'html' => $this->getHtmlPopupBasket(),
            ]
        );
    }
}
