<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\PricelistRubric;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class BlockPricelistRubrics extends Resource
{
    public $model = PricelistRubric::class;

    public $title = 'Название услуги';

    protected $cacheTag = 'block_pricelist_rubrics';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('pricelist_title_id', 'pricelist_title_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Название услуги', 'title')->filter()->language(),
            Text::make('Текст под названием услуги', 'description')->filter()->language(),
            Checkbox::make('Акция', 'is_sale')->filter()->sortable(),
            Definition::make('Описание работы и цена')
                ->hasMany('prices', BlockPricelist::class),
        ];
    }
}
