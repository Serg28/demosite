<?php

namespace App\Cms\Definitions;

use App\Models\Warehouse;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Warehouses extends Resource
{
    public $model = Warehouse::class;

    public $title = 'Склады и поставщики';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название внутреннее', 'title')->filter()->sortable(),
            Text::make('Название для сайта', 'site_title')->language()->filter()->sortable(),
            Checkbox::make('Выводить на сайте', 'is_active')->filter()->sortable()->fastEdit(),
            /*Definition::make('Расписание')
                ->hasMany('warehouseDeliverySchedules', WarehouseDeliverySchedule::class),*/
            Definition::make('Дни и время','daystime')
                ->hasMany('deliverySchedules', DeliverySchedules::class),
        ];
    }
}
