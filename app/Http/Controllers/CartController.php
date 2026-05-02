<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddToCartAction;
use App\Actions\Cart\RemoveFromCartAction;
use App\Actions\Cart\UpdateCartItemAction;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(
        Product $product,
        Request $request,
        AddToCartAction $action,
    ): JsonResponse {
        $qty = (int) $request->input('count', 1);
        $options = (array) $request->input('options', []);

        $result = $action->handle($product, $qty, $options);

        $request->session()->save();

        return response()->json($result->toArray(), $result->status ? 200 : 422);
    }

    public function update(
        string $rowId,
        Request $request,
        UpdateCartItemAction $action,
    ): JsonResponse {
        $qty = (int) $request->input('qty', 1);
        $result = $action->handle($rowId, $qty);

        $request->session()->save();

        return response()->json($result->toArray(), $result->status ? 200 : 422);
    }

    public function remove(
        string $rowId,
        Request $request,
        RemoveFromCartAction $action,
    ): JsonResponse {
        $result = $action->handle($rowId);

        $request->session()->save();

        return response()->json($result->toArray(), $result->status ? 200 : 422);
    }
}
