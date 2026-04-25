<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxProduct;
use App\Models\UnfinishedBasketsProducts;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class UnfinishedBasketsProduct extends Resource
{
    public $model = UnfinishedBasketsProducts::class;

    public $title = 'Продукты';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Брошенная корзина', 'unfinished_basket_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            ForeignAjaxProduct::make('Товар', 'product_id')
                ->options((new Options('product'))
                    ->isJson()),

            Number::make('Количество', 'count'),
            Text::make('Цена, грн', 'price'),
        ];
    }
}
