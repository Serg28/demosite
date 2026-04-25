<?php

namespace App\Cms\Definitions;

use App\Models\OrderProductOption;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;

class OrderProductOptions extends Resource
{
    public $model = OrderProductOption::class;

    public $title = 'Опции';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('order_products_id', 'order_products_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            ForeignAjax::make('Характеристика', 'characteristic_id')
                ->options((new Options('characteristic'))->isJson())
                ->nullable('Выберите характеристику'),

            ForeignAjax::make('Значение', 'characteristic_option_id')
                ->options((new Options('characteristicOption'))->isJson()),
        ];
    }
}
