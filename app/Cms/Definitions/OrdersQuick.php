<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxManager;
use App\Cms\Fields\ForeignAjaxUser;
use App\Cms\Services\ActionsOrders;
use Carbon\Carbon;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Export;

class OrdersQuick extends Orders
{
    public $title = 'Быстрые заказы';

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),

                ForeignAjaxManager::make('Менеджер', 'manager_id')
                    ->sortable()
                    ->filter()
                    ->options((new Options('manager'))
                        ->keyField('first_name')),

                Number::make('Сумма заказа, грн', 'cost')
                    ->filter()
                    ->sortable(),

                Number::make('Стоимость доставки, грн', 'price_delivery')->onlyForm(),
                Checkbox::make('Доставка оплачивается отдельно', 'is_delivery_paid_separately')->onlyForm(),

                Datetime::make('Дата создания', 'created_at')->filter()->sortable()->default(Carbon::now()),
                Hidden::make('Дата обновлнения', 'updated_at')->onlyForm(),
                Hidden::make('Быстрые заказ', 'is_quick')->default(1),
            ],

            'Данные клиента' => [

                ForeignAjaxUser::make('Клиент', 'user_id')
                    ->onlyForm()
                    ->options((new Options('user'))
                        ->keyField('first_name')),

                Text::make('Имя', 'first_name')
                    ->filter()
                    ->sortable(),

                Text::make('Фамилия', 'last_name')
                    ->filter()
                    ->sortable(),

                Text::make('Телефон', 'phone')
                    ->filter()
                    ->sortable(),

                Text::make('Email', 'email')
                    ->onlyForm(),
            ],

            'Товары' => [
                Definition::make('Товары')
                    ->hasMany('products', OrderProducts::class),
            ],
        ];
    }

    public function getFilterScope($collection)
    {
        return $collection->where('is_quick', 1);
    }

    public function buttons(): array
    {
        return [
            Export::class,
        ];
    }

    public function actions()
    {
        return ActionsOrders::make()->orderopen()->insert()->update()->revisions()->delete();
    }
}
