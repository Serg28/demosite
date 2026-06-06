<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddToCart;
use App\Actions\Cart\RemoveFromCart;
use App\Actions\Cart\UpdateCartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(
        Product $product,
        Request $request,
        AddToCart $action,
    ): JsonResponse {
        $qty = (int) $request->input('count', 1);
        $options = (array) $request->input('options', []);

        $result = $action->handle($product, $qty, $options);

        $request->session()->save();

        return response()->json($result->toArray(), $result->status ? 200 : 422);
    }

    /**
     * Масове додавання товарів до кошика (для "Все в кошик").
     * Body: { items: [{id: int, count: int}] }
     */
    public function addBulk(
        Request $request,
        AddToCart $action,
    ): JsonResponse {
        $items = (array) $request->input('items', []);
        $totalCount = 0;
        $addedProducts = [];

        foreach ($items as $item) {
            $productId = (int) ($item['id'] ?? 0);
            $qty       = max(1, (int) ($item['count'] ?? 1));

            $product = Product::query()->find($productId);
            if (! $product) {
                continue;
            }

            $result = $action->handle($product, $qty);
            if ($result->status) {
                $totalCount = $result->count;
                $addedProducts = array_merge($addedProducts, $result->products ?? []);
            }
        }

        $request->session()->save();

        return response()->json([
            'status'   => true,
            'count'    => $totalCount,
            'action'   => 'add',
            'products' => $addedProducts,
        ]);
    }

    public function update(
        string $rowId,
        Request $request,
        UpdateCartItem $action,
    ): JsonResponse {
        $qty = (int) $request->input('qty', 1);
        $result = $action->handle($rowId, $qty);

        $request->session()->save();

        return response()->json($result->toArray(), $result->status ? 200 : 422);
    }

    public function remove(
        string $rowId,
        Request $request,
        RemoveFromCart $action,
    ): JsonResponse {
        $result = $action->handle($rowId);

        $request->session()->save();

        return response()->json($result->toArray(), $result->status ? 200 : 422);
    }
}
