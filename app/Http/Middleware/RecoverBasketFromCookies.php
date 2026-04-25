<?php

namespace App\Http\Middleware;

use App\Services\UnfinishedBasketService;
use Closure;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Http\Request;

class RecoverBasketFromCookies
{
    private $unfinishedBasketService;

    private $cart;

    public function __construct(UnfinishedBasketService $unfinishedBasketService, Cart $cart)
    {
        $this->unfinishedBasketService = $unfinishedBasketService;
        $this->cart = $cart;
    }

    public function handle(Request $request, Closure $next)
    {
        if (\Cart::count() == 0) {
            $products = $this->unfinishedBasketService->getProducts();

            foreach ($products as $product) {
                $productModel = $product->product;

                if ($productModel) {
                    $this->cart->add($productModel->id, $productModel->title, $product->count, $productModel->getPrice())->associate($productModel);
                }
            }
        }

        return $next($request);
    }
}
