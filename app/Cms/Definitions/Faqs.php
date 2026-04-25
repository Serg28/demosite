<?php

namespace App\Cms\Definitions;

use App\Models\Faq;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Faqs extends Resource
{
    public $model = Faq::class;

    public $title = 'Вопросы/ответы';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        $foreign_field_id = request('foreign_field_id') ?: 1;
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Вопрос', 'title')->filter()->sortable()->language(),
            Froala::make('Ответ', 'description')->language(),
            Checkbox::make('Активно', 'is_active')->filter()->sortable(),
            Hidden::make('Tree', 'faq_rubric_id')
                ->onlyForm()
                //->default(request('foreign_field_id')),
                ->default($foreign_field_id),
        ];
    }
}
