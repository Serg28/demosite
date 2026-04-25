<?php

namespace App\Cms\Tree\Templates;

use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\ResourceTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Promotion extends ResourceTree
{
    protected $titleDefinition = 'Акции';

    public $action = 'PromotionController@index';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language(),
                Text::make('Slug', 'slug'),

                /*Text::make('Url', 'url')
                    ->language()
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('tb_tree')->ignore(request('id')),
                    ])->onlyForm(),*/
                Checkbox::make('Активно', 'is_active'),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
