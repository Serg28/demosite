<?php

namespace App\Cms\Definitions;

use App\Cms\Definitions\HasManyBlocks\BannersSlider;
use App\Cms\Definitions\HasManyBlocks\BlockAdvantages;
use App\Cms\Definitions\HasManyBlocks\BlockContactRubrics;
use App\Cms\Definitions\HasManyBlocks\BlockContacts;
use App\Cms\Definitions\HasManyBlocks\BlockFaqRubrics;
use App\Cms\Definitions\HasManyBlocks\BlockPricelistTitle;
use App\Cms\Definitions\HasManyBlocks\BlockWhyWe;
use App\Cms\Definitions\HasManyBlocks\BlockWorkTime;
use App\Cms\Definitions\HasManyBlocks\ClientsLogo;
use App\Cms\Fields\ManyToManyAjaxProducts;
use App\Cms\Fields\ManyToManyOptionsXml;
use App\Cms\Fields\ProductsCodes;
use App\Models\HasMany\Block;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\SelectWithPicture;
use Vis\Builder\Fields\Text;

class BlocksSeocatalog extends Resource
{
    public $model = Block::class;

    public $title = 'Блоки';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('model_id', 'model_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            SelectWithPicture::make('Блок', 'template')
                ->options([
                    '' => [
                        'value' => 'Выбрать блок',
                    ],
                    /*'home_catalog_show' => [
                        'value' => 'Категории (список), на главной странице',
                        'data-img' => glide('/blocks/block_home_catalog_show.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'home_catalog_show'
                    ],*/

                    'brand_select_list' => [
                        'value' => 'Лента логотипов брендов',
                        'data-img' => glide('/blocks/block_brand_select_list.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'brand_select_list'
                    ],

                ])->action(),

            Text::make('Заголовок H2 ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_h2 last_news block_home_why_we brand_select_list')
                ->hasOne('h2')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_home_why_we')
                ->hasOne('description')
                ->onlyForm(),


            Text::make('Надпись на кнопке ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_home_why_we brand_select_list')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Text::make('Ссылка', 'adress')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_home_why_we brand_select_list')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Definition::make('Преимущества')
                ->className('advantages block_home_why_we credit_list')
                ->hasMany('advantages', BlockAdvantages::class)
                ->className('advantages'),

            Checkbox::make('Отображать', 'is_active')->default(1),

            Definition::make('Слайдер')
                ->hasMany('bannersSlider', BannersSlider::class)
                ->className('banner_slider'),

            ManyToMany::make('Дополнительные категории')->options(
                (new Options('favoriteCategories'))->keyField('title')
            )->className('home_catalog_show'),

            ManyToMany::make('Список Брендов')->options(
                (new Options('favoriteBrends'))->keyField('title')
            )->className('brand_select_list'),
//
//            ManyToManyOptionsXml::make('Список Брендов')
//                ->options(
//                    (new Options('brends'))->where('characteristic_id', '=', $this->getThisCharacteristicId())
//                )->className('brand_select_list'),

        ];
    }
}
