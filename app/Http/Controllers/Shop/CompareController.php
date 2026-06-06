<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CompareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Додати товар до порівняння.
     */
    public function add(Product $product, CompareService $compare): JsonResponse
    {
        $added = $compare->add($product);

        if (! $added) {
            return response()->json([
                'status'    => 'error',
                'inCompare' => true,
                'count'     => $compare->count(),
                'message'   => __t('Максимум 4 товари для порівняння'),
            ]);
        }

        return response()->json([
            'status'    => 'success',
            'inCompare' => true,
            'count'     => $compare->count(),
            'message'   => __t('Додано до порівняння'),
            'product'   => $this->productAnalytic($product),
        ]);
    }

    /**
     * Видалити товар з порівняння.
     */
    public function delete(Product $product, CompareService $compare): JsonResponse
    {
        $compare->remove($product);

        return response()->json([
            'status'    => 'success',
            'inCompare' => false,
            'count'     => $compare->count(),
            'message'   => __t('Видалено з порівняння'),
            'product'   => $this->productAnalytic($product),
        ]);
    }

    /**
     * Статус порівняння для масиву ID товарів.
     */
    public function status(Request $request, CompareService $compare): JsonResponse
    {
        return response()->json([
            'inCompare' => $compare->productIds(),
        ]);
    }

    /**
     * Дані товару для аналітики (GA4/Pixel).
     *
     * @return array<string, mixed>
     */
    private function productAnalytic(Product $product): array
    {
        return [
            'item_id'       => $product->id,
            'item_name'     => $product->t('name'),
            'price'         => (float) $product->price,
            'item_category' => $product->category?->t('name'),
        ];
    }
}
