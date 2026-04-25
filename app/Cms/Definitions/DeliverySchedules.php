<?php

namespace App\Cms\Definitions;

use App\Models\DeliverySchedule as DeliveryScheduleModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class DeliverySchedules extends Resource
{
    public $model = DeliveryScheduleModel::class;

    public $title = 'Дни и времяи';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            //Id::make('#', 'id')->sortable(),
            Hidden::make('Склад/поставщик', 'warehouse_id')->default(request('foreign_field_id')),
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

            Definition::make('Способы доставки и информация','warehouseDeliveryScheduleInfo')
                ->hasMany('warehouseDeliveryScheduleInfo', WarehouseDeliveryScheduleInfo::class),

        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->update()->clone()->delete();
    }
}
