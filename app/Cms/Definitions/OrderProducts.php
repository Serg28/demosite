<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxAvailProduct;
use App\Cms\Fields\ForeignAjaxProduct;
use App\Cms\Fields\OrderProductCode;
use App\Cms\Fields\OrderProductNum;
use App\Models\OrderProducts as OrderProductsModel;
use App\Models\Product;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class OrderProducts extends Resource
{
    public $model = OrderProductsModel::class;

    public $title = 'Продукты';

    public function fields(): array
    {
        return [
            OrderProductNum::make('#'),
            Hidden::make('#', 'id')->sortable(),
            Hidden::make('Заказ', 'order_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            OrderProductCode::make('Артикул')->options((new Options('product'))
                ->isJson()),
            /*ForeignAjaxProduct::make('Товар', 'product_id')
                ->options((new Options('product'))
                    ->isJson()),*/
            ForeignAjaxAvailProduct::make('Товар', 'product_id')
                ->options((new Options('product'))
                    ->isJson()),

            Number::make('Количество', 'count')->default(1),
            //Text::make('Цена, грн', 'price'),
            //--
            Text::make('Цена базовая, грн', 'base_price'),
            Text::make('Скидка, %', 'discount'),
            Text::make('Цена продажи, грн', 'price'),

            Text::make('Сумма базовая, грн', 'base_amount')->onlyForm(),
            Text::make('Сумма скидки, грн', 'discount_amount')->onlyForm(),
            Text::make('Сумма со скидкой, грн', 'total_amount'),
            //--

            Definition::make('Опции')
                ->hasMany('options', OrderProductOptions::class),
        ];
    }
}
