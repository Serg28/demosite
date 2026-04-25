<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\CloseFilter;
use App\Models\CategoryCharacteristic;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class CategoryCharacteristics extends Resource
{
    public $model = CategoryCharacteristic::class;

    public $title = 'Фильтры категории';

    //protected $cacheTag = 'сategorycharacteristic';
    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            //Id::make('#', 'id')->sortable(),
            Hidden::make('Категория', 'category_id')->onlyForm()->default(request('foreign_field_id')),
            Foreign::make('Группа', 'group_id')
                ->filter()->sortable()
                ->options((new Options('group'))->isJson())->default(__cms('Без группы')),
            ForeignAjax::make('Характеристика', 'characteristic_id')
                ->options((new Options('characteristic'))
                    ->isJson())->default(''),
            Text::make('Название в фильтре', 'name')->language()->filter()->sortable(),
            Checkbox::make('В фильтре','is_filter')->filter()->sortable()->default(false),
            CloseFilter::make('Закрыт по-умолчанию', 'is_closed')->onlyForm(),
            Checkbox::make('Основная','is_base')->filter()->sortable()->default(false),
        ];
    }

    public function actions()
    {
        return Actions::make()->update()->revisions()->delete();
    }
}
