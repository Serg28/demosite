<?php

namespace App\Cms\Definitions;

use App\Models\WarehouseOrderProduct as WarehouseOP;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Services\Export;

class QuickWarehouseOrderProduct extends Resource
{
    public $model = WarehouseOP::class;

    public $title = 'Заказ товара со склада';

    protected $orderBy = 'id desc';

    public function fields()
    {
        $attributes = json_decode(request('foreign_attributes'), true);

        return [
            Id::make('#', 'id')->sortable(),
            //Text::make('Название склада', 'title')->filter()->sortable()->language(),

            Hidden::make('Заказ', 'order_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            Hidden::make('Товар', 'product_id')
                ->onlyForm()
                ->default($attributes['product']),

            ForeignAjax::make('Склад', 'warehouse_id')
                ->options(
                    (new Options('warehouse'))
                )
                ->filter()->sortable(),

            Text::make('Количество', 'count')->filter()->sortable(),

            Textarea::make('Комментарий', 'comment')
                ->onlyForm(),

            Hidden::make('Дата создания', 'created_at')
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
