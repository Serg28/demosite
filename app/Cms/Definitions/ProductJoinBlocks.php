<?php

namespace App\Cms\Definitions;

use App\Models\ProductJoinBlock;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class ProductJoinBlocks extends Resource
{
    public $model = ProductJoinBlock::class;

    public $title = 'Блок связи';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Продукт', 'product_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Text::make('Заголовок блока', 'title')->filter()->language(),

            ManyToMany::make('Продукты')
                ->options(
                    (new Options('products'))
                )
                ->onlyForm(),
        ];
    }
}
