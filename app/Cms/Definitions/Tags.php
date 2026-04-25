<?php

namespace App\Cms\Definitions;

use App\Models\Tag;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Tags extends Resource
{
    public $model = Tag::class;

    public $title = 'Теги';

    protected $orderBy = 'id desc';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Заголовок', 'title')
                ->language()
                ->filter()
                ->sortable()
                ->transliteration('slug', true),
            Text::make('Url', 'slug')
                ->filter()
                ->sortable()->rules([
                    'required',
                    Rule::unique('tags')->ignore(request('id')),
                ]),
            Checkbox::make('Активно', 'is_active')->filter()->sortable(),
        ];
    }
}
