<?php

namespace App\Cms\Definitions;

use App\Models\OrderStatus;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Color;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class OrdersStatus extends Resource
{
    public $model = OrderStatus::class;

    public $title = 'Статусы заказов';

    protected $orderBy = 'priority desc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),

            Text::make('Название на prom', 'name_for_prom'),

            Color::make('Цвет', 'color'),

            Checkbox::make('Отправлять в 1С', 'send_to_1c')->filter()->sortable()->fastEdit(),
        ];
    }
}
