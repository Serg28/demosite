<?php

namespace App\Cms\Definitions;

use App\Models\OrderProduct;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Hidden;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Text;

class OrderProducts extends Resource
{
    public $model = OrderProduct::class;

    public $title = 'Товари';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Замовлення', 'order_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Text::make('Назва', 'title')->onlyForm(),
            Text::make('Товар (ID)', 'product_id'),
            Number::make('Кількість', 'count'),
            Number::make('Ціна, грн', 'price'),
            Number::make('Базова ціна, грн', 'base_price')->onlyForm(),
            Number::make('Сума, грн', 'amount')->onlyForm(),
        ];
    }
}
