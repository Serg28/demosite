<?php

namespace App\Services\Checkout;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartGuard
{
    /**
     * @param  Collection<int, mixed>  $cartItems
     * @return array{out_of_stock: array<int, array{rowId: string, id: int, name: string}>, price_changed: array<int, array{rowId: string, id: int, name: string, oldPrice: float, newPrice: float}>}
     */
    public function check(Collection $cartItems): array
    {
        if ($cartItems->isEmpty()) {
            return ['out_of_stock' => [], 'price_changed' => []];
        }

        $productIds = $cartItems->pluck('id')->filter()->unique()->values();
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $outOfStock   = [];
        $priceChanged = [];

        foreach ($cartItems as $item) {
            $product = $products->get($item->id);

            if ($product === null || ! $product->is_active || $product->quantity <= 0) {
                $outOfStock[] = [
                    'rowId' => $item->rowId,
                    'id'    => (int) $item->id,
                    'name'  => (string) $item->name,
                ];
            } elseif (abs((float) $product->price - (float) $item->price) > 0.01) {
                $priceChanged[] = [
                    'rowId'    => $item->rowId,
                    'id'       => (int) $item->id,
                    'name'     => (string) $item->name,
                    'oldPrice' => (float) $item->price,
                    'newPrice' => (float) $product->price,
                ];
            }
        }

        return ['out_of_stock' => $outOfStock, 'price_changed' => $priceChanged];
    }
}
