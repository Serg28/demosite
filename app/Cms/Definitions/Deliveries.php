<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ManyToManyAjaxCities;
use App\Models\Delivery;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Deliveries extends Resource
{
    public $model = Delivery::class;

    public $title = 'Доставка';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
            Number::make('Цена, грн', 'price')->filter()->sortable(),
            Number::make('Бесплатно от, грн', 'free_cost')->filter()->sortable(),
            Froala::make('Описание', 'description')->filter()->sortable()->language(),
            Select::make('Тип доставки', 'type')
                ->options([
                    '' => 'Без типа',
                    'np' => 'Новая почта (в отделение)',
                    'np_address' => 'Новая почта (адресная)',
                    'pickup' => 'Самовывоз',
                    'ukrposhta' => 'Укрпочта',
                    'justin' => 'Justin',
                    'meest' => 'Meest',
                ])
            ->onlyForm()->action(),

            Definition::make('Точки доставки')
                ->hasMany('points', DeliveryPickupPoints::class)
                ->className('pickup'),

            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),

            Checkbox::make('Отображать для всех городов', 'is_show_for_all_cities')->filter()->sortable(),

            ManyToManyAjaxCities::make('Отображать в городах')
                ->options(
                    (new Options('cities'))
                ),

            ManyToMany::make('Отображать методы оплаты')
                ->options(
                    (new Options('payments'))
                ),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
