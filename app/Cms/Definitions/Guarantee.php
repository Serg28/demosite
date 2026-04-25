<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignTreeCategory;
use App\Models\Guarantee as GuaranteeModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;

class Guarantee extends Resource
{
    public $model = GuaranteeModel::class;

    public $title = 'Гарантия';
    //protected $orderBy = 'priority asc';
    //protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            ForeignTreeCategory::make('Категория', 'category_id')
                ->options((new Options('category'))->isJson())
                ->sortable()->filter(),
            ForeignAjax::make('Бренд', 'brand_id')
                ->options((new Options('brand'))->isJson())
                ->sortable()->filter(),
            Froala::make('Гарантия (блок у товара)', 'guarantee')->language()->onlyForm(),
            Checkbox::make('Показать', 'is_active')->filter()->sortable()->fastEdit(),
        ];
    }
}
