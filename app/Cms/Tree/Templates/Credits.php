<?php

namespace App\Cms\Tree\Templates;

use App\Cms\Fields\Tinymce;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\ResourceTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class Credits extends ResourceTree
{
    protected $titleDefinition = 'Умови кредитування';

    public $action = 'ArticleController@creditList';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language(),
                Text::make('Короткое Описание', 'short_description')->language(),
                Tinymce::make('Описание', 'description')->language(),
                Text::make('Slug (old url)', 'slug'),

                Text::make('Url', 'url')
                    ->language()
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('tb_tree')->ignore(request('id')),
                    ])->onlyForm(),

                Image::make('Картинка', 'picture'),
                Checkbox::make('Активно', 'is_active'),
            ],
//            'Блоки' => [
//                Definition::make('Блоки')
//                    ->morphMany('blocks', Blocks::class),
//            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
