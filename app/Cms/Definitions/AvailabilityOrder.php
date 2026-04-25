<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxProduct;
use App\Cms\Fields\ForeignAjaxUser;
use App\Models\AvailabilityOrder as Model;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class AvailabilityOrder extends Resource
{
    public $model = Model::class;

    public $title = 'Сообщить на наличие товара';

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Имя', 'name')->sortable()->filter(),
                Text::make('Email', 'email')->sortable()->filter(),
                Text::make('Телефон', 'phone')->sortable()->filter(),
                ForeignAjaxProduct::make('Товар', 'product_id')
                    ->filter()->sortable()
                    ->options((new Options('product'))->isJson()),
                ForeignAjaxUser::make('Пользователь', 'user_id')
                    ->options((new Options('user'))->keyField('first_name'))
                    ->filter(),

            ],

        ];
    }

    public function actions()
    {
        return Actions::make()->update()->delete();
    }
}
