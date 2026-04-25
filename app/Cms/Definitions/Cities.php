<?php

namespace App\Cms\Definitions;

use App\Models\City;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class Cities extends Resource
{
    public $model = City::class;

    public $title = 'Нова Почта города';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [

            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
            Foreign::make('Регион', 'region_id')
                ->options((new Options('regions'))
                    ->isJson())
                ->filter(),
            Foreign::make('Тип населенного пункта', 'type_id')
                ->options((new Options('settlements'))
                    ->isJson())
                ->filter(),
            ManyToMany::make('Доставка')
            ->options(
                (new Options('deliveries'))->where('is_show_for_all_cities', '=', '0')
            ),
        ];
    }
}
