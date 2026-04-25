<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\PricelistTitle;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class BlockPricelistTitle extends Resource
{
    public $model = PricelistTitle::class;

    public $title = 'Категория прайс-листа (напр., Рама)';

    protected $cacheTag = 'block_pricelist_title';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Категория прайс-листа', 'title')->filter()->language(),
            Definition::make('Прайс-лист')
                ->hasMany('rubrics', BlockPricelistRubrics::class),
            //->className('pricelists'),
        ];
    }
}
