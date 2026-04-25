<?php

declare(strict_types=1);

namespace App\Http\ViewComposers;

use App\Models\CharacteristicRelation;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProductGroupJoinComposer
{
    public function compose(View $view): void
    {
        debugbar()->startMeasure('ProductGroupJoinComposer', 'Time for ProductGroupJoinComposer');
        $group = $this->provideRelatedProducts($view->page);

        $selfCharacteristics = $this->provideSelfCharacteristics($view->page);

        $view->with(compact('group', 'selfCharacteristics'));
        debugbar()->stopMeasure('ProductGroupJoinComposer');
    }

    private function provideSelfCharacteristics(Product $product): Collection
    {
        return $product->characteristics->mapWithKeys(fn ($characteristic) => [
            $characteristic->characteristic->t('title') => $characteristic->characteristicOption->t('title'),
        ]);
    }

    private function provideRelatedProducts(Product $product): Collection
    {
        return $product->relatedProducts->mapWithKeys(fn (CharacteristicRelation $relation) => [
            $relation->characteristic->t('title') => [
                'products' => $relation->products->map(fn (Product $product) => [
                    'enable' => $this->isProductEnable($product),
                    'url' => $product->getUrl(),
                    'title' => $product->characteristics
                        ->firstWhere('characteristic_id', '=', $relation->characteristic->id)
                        ->characteristicOption->t('title'),
                ]),
                'characteristic' => $relation->characteristic->t('title'),
                'characteristic_id' => $relation->characteristic_id,
            ],
        ]);
    }

    private function isProductEnable(Product $product): bool
    {
        return (bool) $product->is_active;
    }
}
