<?php

namespace App\Cms\Definitions;

use App\Models\CharacteristicOption;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class CharacteristicOptionsForDefinition extends Resource
{
    public $model = CharacteristicOption::class;

    public $title = 'Опции';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('Продукт', 'characteristic_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            Text::make('Название', 'title')
                ->filter()
                ->sortable()
                ->language()
                ->transliteration('slug', true),

            Text::make('Url', 'slug')
                ->filter()
                ->sortable()->rules([
                    'required',
                    Rule::unique('characteristic_options')->ignore(request('id')),
                ]),
            Number::make('Порядок сортировки', 'priority')->filter()->sortable(),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
