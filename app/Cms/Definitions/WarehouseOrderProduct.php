<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxProduct;
use App\Cms\Fields\ForeignOrder;
use App\Models\WarehouseOrderProduct as WarehouseOP;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Services\Export;

class WarehouseOrderProduct extends Resource
{
    public $model = WarehouseOP::class;

    public $title = 'Заказ товаров на складах';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            //Text::make('Название склада', 'title')->filter()->sortable()->language(),
            ForeignOrder::make('Заказ', 'order_id')
                ->options(
                    (new Options('order'))
                )
                ->filter()->sortable(),
            ForeignAjax::make('Склад', 'warehouse_id')
                ->options(
                    (new Options('warehouse'))->isJson()
                )
                ->filter()->sortable(),

            ForeignAjaxProduct::make('Товар', 'product_id')
                ->options(
                    (new Options('product'))->isJson()
                )
                ->filter()->sortable(),
            Text::make('Количество', 'count')->filter()->sortable(),

            Select::make('Статус заказа', 'status')
                ->options([
                    '' => 'Без статуса',
                    'Не заказан' => 'Не заказан',
                    'Заказан' => 'Заказан',
                    'Доставляется' => 'Доставляется',
                    'Доставлен' => 'Доставлен',
                    'Отменен' => 'Отменен',
                ])
                ->filter()
                ->sortable()
                ->action(),

            Textarea::make('Комментарий', 'comment')
                ->onlyForm(),

            Datetime::make('Дата создания', 'created_at')
                ->filter()
                ->sortable()
                ->default(Carbon::now()),

            Hidden::make('Дата обновлнения', 'updated_at')
                ->onlyForm(),
        ];
    }

    public function buttons(): array
    {
        return [
            Export::class,
        ];
    }
}
