<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\FaqRubric;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class BlockFaqRubrics extends Resource
{
    public $model = FaqRubric::class;

    public $title = 'Блок faq рубрики';

    protected $cacheTag = 'block_faq_rubrics';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Рубрика', 'title')->filter()->language(),
            Definition::make('Блоки FAQ')
                ->hasMany('faqs', BlockFaq::class),
        ];
    }
}
