<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowPriceRequest;
use App\Models\FollowPrice;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class FollowPriceController extends BaseController
{
    public function add(Product $product, FollowPriceRequest $request): JsonResponse
    {
        FollowPrice::create([
            'user_id' => app('user') ? app('user')->id : null,
            'product_id' => $product->id,
            'email' => $request->get('email'),
        ]);

        return $this->returnSuccess('Товар доданий');
    }
}
