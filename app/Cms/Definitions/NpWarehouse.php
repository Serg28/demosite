<?php

namespace App\Cms\Definitions;

use App\Models\NPWarehouse as NPWarehouseModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class NpWarehouse extends Resource
{
    public $model = NPWarehouseModel::class;

    public $title = 'Нова Почта отделения';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
            ForeignAjax::make('Город', 'city_id')
                ->options(
                    (new Options('city'))->isJson()
                )
                ->filter()->sortable(),
        ];
    }
}
