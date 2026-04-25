<?php

namespace App\Cms\Definitions;

use App\Models\Question;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Questions extends Resource
{
    public $model = Question::class;

    public $title = 'Блоки';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),

            Hidden::make('Tree', 'tree_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Checkbox::make('Активно', 'is_active')->filter()->sortable(),
            Definition::make('FAQ')
                ->hasMany('faq', Faqs::class),
        ];
    }
}
