<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Pricelist;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class BlockPricelist extends Resource
{
    public $model = Pricelist::class;

    public $title = 'Описание работы и цена';

    protected $cacheTag = 'block_pricelist';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('pricelist_rubric_id', 'pricelist_rubric_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Описание работы', 'title')->filter()->language(),
            Text::make('Цена, грн', 'price')->language()->sortable(),
            Text::make('Цена старая, грн', 'price_old')->language()->sortable(),
            Checkbox::make('Показывать', 'is_active')->filter()->sortable(),
        ];
    }
}
