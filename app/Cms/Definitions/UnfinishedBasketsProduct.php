<?php

namespace App\Cms\Definitions;

use App\Models\UnfinishedBasketsProducts;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Hidden;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Text;

class UnfinishedBasketsProduct extends Resource
{
    public $model = UnfinishedBasketsProducts::class;

    public $title = 'Товари';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Кошик', 'unfinished_basket_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Text::make('Товар (ID)', 'product_id'),
            Number::make('Кількість', 'count'),
            Text::make('Ціна, грн', 'price'),
        ];
    }
}
