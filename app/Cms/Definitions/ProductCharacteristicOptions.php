<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxCharacteristicOptions;
use App\Cms\Fields\ForeignCharacteristic;
use App\Models\ProductCharacteristicOption;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;

class ProductCharacteristicOptions extends Resource
{
    public $model = ProductCharacteristicOption::class;

    public $title = 'Характеристики';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),

            Hidden::make('Продукт', 'product_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            ForeignCharacteristic::make('Характеристика', 'characteristic_id')
                ->options((new Options('characteristic'))->isJson())
                    ->nullable('Выберите характеристику'),

            ForeignAjaxCharacteristicOptions::make('Значение', 'characteristic_option_id')
                ->options((new Options('characteristicOption'))->isJson()),

            Text::make('Произвольное значение', 'characteristic_option_value')->language()->comment('Текстовое значение характеристики, если есть необходимость не выбирать ее из предоставленных выше значений. Внимание! Такие значения не могут использоваться в фильтрации товаров в каталоге')
        ];
    }
}
