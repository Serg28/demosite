<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ManyToManyAjaxProducts;
use App\Models\CharacteristicRelation;
use App\Rules\ProductsHasCharacteristicRule;
use App\Rules\SameProductsCategoryRule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class RelatedProductsByCharacteristic extends Resource
{
    public $model = CharacteristicRelation::class;

    public $title = 'Связи товаров';

    protected $orderBy = 'id asc';

    protected $isSortable = false;

    protected $cacheTag = 'related_products_by_characteristic';

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title'),
            Foreign::make('Связывающая характеристика', 'characteristic_id')
                ->options((new Options('characteristic'))->isJson())
                ->filter(),
            ManyToManyAjaxProducts::make('Товары', 'products')
                ->options((new Options('products'))->isJson())
                ->rules([
                    new SameProductsCategoryRule(),
                    ProductsHasCharacteristicRule::make((int) request('characteristic_id')),
                ])->onlyForm(false),
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->delete()->clone();
    }
}
