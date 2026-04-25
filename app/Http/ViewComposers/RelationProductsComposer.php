<?php

namespace App\Http\ViewComposers;

use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class RelationProductsComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('RelationProductsComposer', 'Time for RelationProductsComposer');
        $relationProducts = Cache::tags(['products', 'characteristics', 'characteristic_options', 'сategories'])
            ->rememberForever('relative_product_' . $view->page->getCacheKey(), function () use ($view) {
                $productPage = $view->page;
                $relationProducts = [];
                $products = [];

                $category = $productPage->category->characteristic_id;
                $characteristicsRelatives = $productPage->category->characteristicsForProductRelative->pluck('id');

                $characteristicsProduct = $productPage->characteristics()->whereIn(
                    'characteristic_id',
                    $characteristicsRelatives
                )->pluck('characteristic_option_id');

                if ($category && count($characteristicsRelatives)) {
                    $modelCharacteristic = $productPage->characteristics()->where(
                        'characteristic_id',
                        $category
                    )->first();

                    if ($productPage->category_id && $productPage->category_id && $modelCharacteristic) {
                        $products = Product::with(['characteristics'])
                            ->leftJoin(
                                'product_characteristic_options',
                                'products.id',
                                '=',
                                'product_characteristic_options.product_id'
                            )
                            ->where('products.category_id', $productPage->category_id)
                            ->where('characteristic_id', $modelCharacteristic->characteristic_id)
                            ->where('characteristic_option_id', $modelCharacteristic->characteristic_option_id)
                            ->where('products.is_active', 1)
                            ->where('products.quantity', '>', 0)
                            ->whereHas('characteristics', function ($q) use ($characteristicsProduct) {
                                $q->whereIn('characteristic_option_id', $characteristicsProduct);
                            })
                            ->select(['products.*'])
                            ->get();

                        $products->push($productPage);
                    }

                    if ($products) {
                        foreach ($characteristicsRelatives as $showOption) {
                            $options = ProductCharacteristicOption::with([
                                'product',
                                'characteristic',
                                'characteristicOption',
                            ])
                                ->leftJoin(
                                    'characteristic_options',
                                    'characteristic_options.id',
                                    '=',
                                    'product_characteristic_options.characteristic_option_id'
                                )
                                ->whereIn('product_id', $products->pluck('id'))
                                ->where('product_characteristic_options.characteristic_id', $showOption)
                                ->where(function ($query) use ($productPage, $characteristicsProduct) {
                                    $query->whereNotIn(
                                        'product_characteristic_options.characteristic_option_id',
                                        $characteristicsProduct
                                    )
                                        ->orWhere('product_id', $productPage->id);
                                })
                                ->orderBy('characteristic_options.title')
                                ->get();

                            foreach ($options as $option) {
                                $relationProducts[__t('Виберіть ' . mb_strtolower($option->characteristic->t('title')))][] = [
                                    'title' => $option->characteristicOption->t('title'),
                                    'product' => $option->product,
                                ];
                            }
                        }
                    }
                }

                return $relationProducts;
            });

        $view->with(compact('relationProducts'));
        debugbar()->stopMeasure('RelationProductsComposer');
    }
}
