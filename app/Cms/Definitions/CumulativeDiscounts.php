<?php

namespace App\Cms\Definitions;

use App\Models\CumulativeDiscount;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Services\Actions;

class CumulativeDiscounts extends Resource
{
    public $model = CumulativeDiscount::class;

    public $title = 'Накопительные скидки';

    protected $orderBy = 'discount asc';

    protected $isSortable = false;

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Number::make('Сумма заказов от', 'total_sum')->filter()->sortable(),
            Number::make('Скидка, %', 'discount')->filter()->sortable(),
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->delete();
    }
}
