<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Перемкнути стан "вибране" для товару.
     * Тільки для авторизованих (middleware 'auth' на маршруті).
     */
    public function toggle(Product $product): JsonResponse
    {
        $user = Auth::user();
        $exists = Wishlist::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            Wishlist::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->delete();

            $isActive = false;
            $action   = 'remove';
            $message  = __t('Видалено зі списку бажань');
        } else {
            try {
                Wishlist::create([
                    'user_id'    => $user->id,
                    'product_id' => $product->id,
                ]);
            } catch (UniqueConstraintViolationException) {
                // подвійний клік / race condition — запис вже існує
            }
            $isActive = true;
            $action   = 'add';
            $message  = __t('Додано до списку бажань');
        }

        return response()->json([
            'isActive' => $isActive,
            'action'   => $action,
            'message'  => $message,
            'status'   => 'success',
            'product'  => [
                'item_id'       => $product->id,
                'item_name'     => $product->t('name'),
                'price'         => (float) $product->price,
                'item_category' => $product->category?->t('name'),
            ],
        ]);
    }

    /**
     * Статус вибраного для масиву ID товарів.
     */
    public function status(Request $request): JsonResponse
    {
        $user = Auth::user();
        $ids = array_map('intval', (array) $request->input('productIds', []));

        if (empty($ids) || ! $user) {
            return response()->json(['favorites' => []]);
        }

        $favorites = Wishlist::query()
            ->where('user_id', $user->id)
            ->whereIn('product_id', $ids)
            ->pluck('product_id')
            ->toArray();

        return response()->json(['favorites' => $favorites]);
    }
}
