<?php

namespace App\Cms\Tree\Templates;

use App\Cms\Definitions\Blocks;
use App\Cms\Definitions\BlocksUniversal;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class Universal extends Node
{
    protected $titleDefinition = 'Универсальный шаблон';

    public $action = 'ArticleController@universal';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language(),
                //Froala::make('Описание', 'description')->language(),
                Text::make('Slug', 'slug'),

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
            'Блоки' => [
                Definition::make('Блоки')
                    ->morphMany('blocks', BlocksUniversal::class),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
