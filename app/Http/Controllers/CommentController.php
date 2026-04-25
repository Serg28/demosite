<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment as CommentRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class CommentController extends BaseController
{
    public function send(
        Product $product,
        CommentRequest $request
    ): JsonResponse {
        /*$product->comment()->add($request);

        return $this->returnSuccess('Коментарій доданий і буде опублікований після модерації');*/

        $result = $product->comment()->add($request);

        if ($result) {
            return $this->returnSuccess(
                "Коментарій доданий і буде опублікований після модерації"
            );
        }
        return $this->returnError(
            "Произошла ошибка во время добавления комментария. Попробуйте немного позже."
        );
    }
}
