<?php

namespace App\Cms\Definitions;

use App\Models\WarehouseDeliveryScheduleInfo as WarehouseDeliveryScheduleInfoModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;

class WarehouseDeliveryScheduleInfo extends Resource
{
    public $model = WarehouseDeliveryScheduleInfoModel::class;

    public $title = 'Способы доставки и информация';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            //Id::make('#', 'id')->sortable(),
            Hidden::make('Дата и время', 'delivery_schedules_id')->default(request('foreign_field_id')),
            ForeignAjax::make('Способ доставки', 'delivery_id')
                ->options(
                    (new Options('delivery'))->isJson()
                )
                ->filter()->sortable(),
            Select::make('Дней до отправки (используется для расчета макс. даты)', 'days_to_delivery')
                ->options([
                    0 => 'Сегодня',
                    1 => 'Сегодня +1',
                    2 => 'Сегодня +2',
                    3 => 'Сегодня +3',
                    4 => 'Сегодня +4',
                    5 => 'Сегодня +5',
                    6 => 'Сегодня +6',
                    7 => 'Сегодня +7',
                    8 => 'Сегодня +8',
                    9 => 'Сегодня +9',
                    10 => 'Сегодня +10'
                ])->onlyForm(),
            Text::make('Текст', 'description')->language(),
        ];
    }
}
