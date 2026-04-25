<?php

namespace App\Cms\Tree\Templates;

use App\Cms\Definitions\Blocks;
use App\Cms\Definitions\BlocksPayDelivery;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class PayDelivery extends Node
{
    protected $titleDefinition = 'Оплата и Доставка';

    public $action = 'ArticleController@payDelivery';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language(),
                //Froala::make('УВАГА!', 'description')->language(),
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
                    ->morphMany('blocks', BlocksPayDelivery::class),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
