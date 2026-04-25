<?php

namespace App\Cms\Definitions;

use App\Cms\Exports\UnfinishedBasketsExport;
use App\Cms\Fields\ButtonInUnfinishBasket;
use App\Cms\Fields\ForeignAjaxUser;
use App\Models\UnfinishedBasket;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\ReadonlyField;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Services\Actions;

class UnfinishedBaskets extends Resource
{
    public $model = UnfinishedBasket::class;

    public $title = 'Брошенные корзины';

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                ReadonlyField::make('Хеш корзины', 'hash_basket'),
                ForeignAjaxUser::make('Клиент', 'user_id')
                    ->options((new Options('user'))
                        ->keyField('first_name')),

                ReadonlyField::make('Дата создания', 'created_at')->filter()->sortable()->default(Carbon::now()),
                ReadonlyField::make('Дата обновление', 'updated_at')->filter()->sortable()->default(Carbon::now()),
                ButtonInUnfinishBasket::make('Кнопка сделать заказ')->onlyForm(),

            ],
            'Товары' => [
                Definition::make('Товары')
                    ->hasMany('products', UnfinishedBasketsProduct::class),
            ],
        ];
    }

    public function buttons(): array
    {
        return [
            UnfinishedBasketsExport::class,
        ];
    }

    public function actions()
    {
        return Actions::make()->update()->delete();
    }
}
