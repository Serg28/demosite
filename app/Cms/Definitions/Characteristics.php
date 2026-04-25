<?php

namespace App\Cms\Definitions;

use App\Models\Characteristic;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Characteristics extends Resource
{
    public $model = Characteristic::class;

    public $title = 'Характеристики';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'Общая' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Название', 'title')
                    ->filter()
                    ->sortable()
                    ->language()
                    ->transliteration('slug', true),

                Text::make('Slug (old url)', 'slug')
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('characteristics')->ignore(request('id')),
                    ]),

                Text::make('Url', 'url')
                    ->language()
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('characteristics')->ignore(request('id')),
                    ])->onlyForm(),

                Checkbox::make('Опция продукта', 'is_option_product')->filter()->sortable(),

                Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
            ],
            'Опции' => [
                Definition::make('Опции')
                    ->hasMany('options', CharacteristicOptionsForDefinition::class),
            ],
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
