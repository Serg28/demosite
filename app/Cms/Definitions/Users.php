<?php

namespace App\Cms\Definitions;

use App\Cms\Cards\ChartUser;
use App\Cms\Cards\NewUsers;
use App\Cms\Cards\SumUsers;
use App\Models\User;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Password;
use Vis\Builder\Fields\ReadonlyField;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\ExportUsers;

class Users extends Resource
{
    public $model = User::class;

    public $title = 'Пользователи';

    public function fields()
    {
        return [
            'Общая' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Email', 'email')->sortable()->filter(),
                Password::make('Пароль', 'password')->onlyForm(),
                Text::make('Фамилия', 'last_name')->sortable()->filter(),
                Text::make('Имя', 'first_name')->sortable()->filter(),
                Text::make('Отчество', 'patronymic')->sortable()->filter(),
                Text::make('Телефон', 'phone')->sortable()->filter(),
                Image::make('Фото', 'image'),
                Text::make('Скидка персональная, %', 'discount')->default(0),
                ReadonlyField::make('Скидка накопительная, %', 'discount_cumulative')->default(0),
                /*ForeignDiscountCard::make('Дисконтная карта', 'discount_id')
                    ->onlyForm()
                    ->options(new Options('discountcard'))
                    ->filter(),*/

                Checkbox::make('Активен', 'completed')->hasOne('activation'),
                ReadonlyField::make('Дата регистрации', 'created_at')->default(Carbon::now())->sortable(),
                ReadonlyField::make('Дата последнего входа', 'last_login')->sortable()->onlyForm(),
            ],

            'Заказы' => [
                Definition::make('Заказы')
                    ->hasMany('orders', OrdersMini::class),
            ],

            'Брошенные корзины' => [
                Definition::make('Брошенные корзины')
                    ->hasMany('unfinishedBasket', UnfinishedBaskets::class),
            ],

            'Группа' => [
                ManyToMany::make('Группа')->options(
                    (new Options('groups'))->keyField('name')
                ),
            ],

        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }

    public function cards()
    {
        return [
            NewUsers::class,
            SumUsers::class,
            ChartUser::class,
        ];
    }

    public function buttons()
    {
        return [
            ExportUsers::class,
        ];
    }
}
