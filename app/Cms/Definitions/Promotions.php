<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ManyToManyAjaxProducts;
use App\Cms\Fields\ProductsCodes;
use App\Cms\Fields\PromotionProductsCodes;
use App\Models\Promotion;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Promotions extends Resource
{
    public $model = Promotion::class;

    public $title = 'Акционные предложения';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Заголовок', 'title')->filter()->sortable()->language()->transliteration('slug', true),
            Text::make('Url', 'slug')
                ->filter()
                ->sortable()->rules([
                    'required',
                    Rule::unique('promotions')->ignore(request('id')),
                ])->onlyForm(),
            Froala::make('Краткое описание', 'short_description')->filter()->sortable()->language()->onlyForm(),
            Froala::make('Полное описание', 'description')->filter()->sortable()->language()->onlyForm(),
            Image::make('Баннер', 'picture')->filter()->sortable()->onlyForm(),
            Datetime::make('Дата начала акции', 'date_start')->filter()->sortable(),
            Datetime::make('Дата окончания акции', 'date_finish')->filter()->sortable(),
            /*ManyToManyAjaxProducts::make('Товары', 'products')
                ->options((new Options('products'))->isJson())
                ->onlyForm(false),*/
            PromotionProductsCodes::make('Товары')
                ->options((new Options('promotionCodeProducts'))->isJson())
                ->onlyForm(false),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable()->fastEdit(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }
}
