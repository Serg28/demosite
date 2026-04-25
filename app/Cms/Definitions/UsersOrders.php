<?php

namespace App\Cms\Definitions;

use App\Models\User;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Services\Actions;

class UsersOrders extends Resource
{
    public $model = User::class;

    public $title = 'Заказы пользователя';

    public function fields()
    {
        return [
            Definition::make('Заказы')
                ->hasMany('orders', Orders::class),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
