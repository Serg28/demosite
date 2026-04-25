<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxProduct;
use App\Models\ProductJoinProduct;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class ProductJoinProducts extends Resource
{
    public $model = ProductJoinProduct::class;

    public $title = 'Продукты';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Продукт', 'product_join_block_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->language(),

            ForeignAjaxProduct::make('Товар', 'product_id')
                ->options((new Options('product'))
                    ->isJson()),
        ];
    }
}
