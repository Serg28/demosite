<?php

namespace App\Cms\Definitions;

use App\Models\DeliveryPickupPoint;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class DeliveryPickupPoints extends Resource
{
    public $model = DeliveryPickupPoint::class;

    public $title = 'Точки доставки';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Доставка', 'delivery_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Text::make('Название', 'title')->filter()->sortable()->language(),
            Text::make('Адрес', 'address')->filter()->sortable()->language(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
