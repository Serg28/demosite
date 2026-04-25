<?php

namespace App\Cms\Definitions;

use App\Models\Brand;
use App\Models\CharacteristicGroupName;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Text;

class CharacteristicGroupNames extends Resource
{
    public $model = CharacteristicGroupName::class;

    public $title = 'Названия групп характеристик';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Название', 'title')->language()->filter()->sortable(),
                Text::make('Код 1С', 'id_1c')->onlyForm(),
                Number::make('Порядок сортировки', 'priority')->filter()->sortable(),
            ]
        ];
    }
}
