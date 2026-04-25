<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxProduct;
use App\Cms\Fields\ForeignAjaxUser;
use App\Models\FollowPrice;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class FollowPrices extends Resource
{
    public $model = FollowPrice::class;

    public $title = 'Отслеживание цен';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Email', 'email')->filter()->sortable(),

            ForeignAjaxProduct::make('Товар', 'product_id')
                ->filter()->sortable()
                ->options((new Options('product'))->isJson()),

            ForeignAjaxUser::make('Клиент', 'user_id')
                ->filter()->sortable()
                ->options((new Options('user'))
                    ->keyField('first_name')),
        ];
    }
}
