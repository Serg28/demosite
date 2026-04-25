<?php

namespace App\Cms\Definitions;

use App\Models\CharacteristicOption;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Color;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class CharacteristicOptions extends Resource
{
    public $model = CharacteristicOption::class;

    public $title = 'Опции';

    protected $orderBy = 'id asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Foreign::make('Характеристики', 'characteristic_id')
                ->options((new Options('characteristic'))
                    ->isJson())
                ->filter()->sortable(),
            Text::make('Название', 'title')
                ->filter()
                ->sortable()
                ->language()
                ->transliteration('slug', true),

            Text::make('Slug (old url)', 'slug')
                ->filter()
                ->sortable()->rules([
                    'required',
                ]),

            Text::make('Url', 'url')
                ->language()
                ->filter()
                ->sortable()->rules([
                    'required',
                ])->onlyForm(),

            Color::make('Цвет', 'color')->onlyForm(),

            Number::make('Порядок сортировки', 'priority')->filter()->sortable(),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
