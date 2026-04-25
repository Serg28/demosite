<?php

namespace App\Cms\Definitions;

use App\Models\WarehouseDeliverySchedule as WarehouseDeliveryScheduleModel;
use Illuminate\Support\Timebox;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;

class WarehouseDeliverySchedule extends Resource
{
    public $model = WarehouseDeliveryScheduleModel::class;

    public $title = 'Расписание доставки';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            //Id::make('#', 'id')->sortable(),
            Hidden::make('Склад/поставщик', 'warehouse_id')->default(request('foreign_field_id')),
            ForeignAjax::make('Способ доставки', 'delivery_id')
                ->options(
                    (new Options('deliveries'))->isJson()
                )
                ->filter()->sortable(),
            Select::make('День недели', 'day_of_week')
            ->options([
                1 => 'Понедельник',
                2 => 'Вторник',
                3 => 'Среда',
                4 => 'Четверг',
                5 => 'Пятница',
                6 => 'Суббота',
                7 => 'Воскресенье',
            ]),
            Select::make('Время от','start_time')
                ->options([
                    '00:00:00' => '00:00',
                    '01:00:00' => '01:00',
                    '02:00:00' => '02:00',
                    '03:00:00' => '03:00',
                    '04:00:00' => '04:00',
                    '05:00:00' => '05:00',
                    '06:00:00' => '06:00',
                    '07:00:00' => '07:00',
                    '08:00:00' => '08:00',
                    '09:00:00' => '09:00',
                    '10:00:00' => '10:00',
                    '11:00:00' => '11:00',
                    '12:00:00' => '12:00',
                    '13:00:00' => '13:00',
                    '14:00:00' => '14:00',
                    '15:00:00' => '15:00',
                    '16:00:00' => '16:00',
                    '17:00:00' => '17:00',
                    '18:00:00' => '18:00',
                    '19:00:00' => '19:00',
                    '20:00:00' => '20:00',
                    '21:00:00' => '21:00',
                    '22:00:00' => '22:00',
                    '23:00:00' => '23:00',
                    '24:00:00' => '24:00'
                ]),
            Select::make('Время до','end_time')
                ->options([
                    '00:00:00' => '00:00',
                    '01:00:00' => '01:00',
                    '02:00:00' => '02:00',
                    '03:00:00' => '03:00',
                    '04:00:00' => '04:00',
                    '05:00:00' => '05:00',
                    '06:00:00' => '06:00',
                    '07:00:00' => '07:00',
                    '08:00:00' => '08:00',
                    '09:00:00' => '09:00',
                    '10:00:00' => '10:00',
                    '11:00:00' => '11:00',
                    '12:00:00' => '12:00',
                    '13:00:00' => '13:00',
                    '14:00:00' => '14:00',
                    '15:00:00' => '15:00',
                    '16:00:00' => '16:00',
                    '17:00:00' => '17:00',
                    '18:00:00' => '18:00',
                    '19:00:00' => '19:00',
                    '20:00:00' => '20:00',
                    '21:00:00' => '21:00',
                    '22:00:00' => '22:00',
                    '23:00:00' => '23:00',
                    '24:00:00' => '24:00'
                ]),
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
