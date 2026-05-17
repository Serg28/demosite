<?php

namespace App\Cms\Definitions;

use App\Models\UnfinishedBasket;
use Carbon\Carbon;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Definition;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\ReadonlyField;
use Linecore\Cms\Services\Actions;

class UnfinishedBaskets extends Resource
{
    public $model = UnfinishedBasket::class;

    public $title = 'Недозавершені кошики';

    public function fields(): array
    {
        return [
            'Основне' => [
                Id::make('#', 'id')->sortable(),
                ReadonlyField::make('Хеш кошика', 'hash_basket'),
                ReadonlyField::make('Клієнт (user_id)', 'user_id')->filter()->sortable(),
                ReadonlyField::make('Дата створення', 'created_at')->filter()->sortable()->default(Carbon::now()),
                ReadonlyField::make('Дата оновлення', 'updated_at')->filter()->sortable()->default(Carbon::now()),
            ],
            'Товари' => [
                Definition::make('Товари')
                    ->hasMany('products', UnfinishedBasketsProduct::class),
            ],
        ];
    }

    public function actions(): Actions
    {
        return Actions::make($this)->update()->delete();
    }
}
