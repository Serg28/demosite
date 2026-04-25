<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Faq;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class BlockFaq extends Resource
{
    public $model = Faq::class;

    public $title = 'Блок faq';

    protected $cacheTag = 'block_faq';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('faq_rubric_id', 'faq_rubric_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Вопрос', 'title')->filter()->language(),
            Froala::make('Ответ', 'description')->filter()->language(),
        ];
    }
}
