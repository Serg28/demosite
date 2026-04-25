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

class BlocksAbout extends Resource
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
                    'about_baner' => [
                        'value' => 'Баннер на странице О нас',
                        'data-img' => glide('/blocks/block_about_baner.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'about_baner'
                    ],
                    'block_about_text_mt_34' => [
                        'value' => 'Блок текста 34, на странице О нас',
                        'data-img' => glide('/blocks/block_about_text-mt-34', ['w' => 400, 'h' => 400]),
                        'data-class' => 'block_about_text_mt_34'
                    ],
                    'block_about_text_mt_40' => [
                        'value' => 'Блок текста 40, на странице О нас',
                        'data-img' => glide('/blocks/block_about_text-mt-40', ['w' => 400, 'h' => 400]),
                        'data-class' => 'block_about_text_mt_40'
                    ],
                    'block_about_why_we' => [
                        'value' => 'Переваги які ми пропонуємо, на странице О нас',
                        'data-img' => glide('/blocks/block_about_why_we.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'block_about_why_we'
                    ],

                ])->action(),

            Text::make('Заголовок H2 ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_h2 reviews pricelists credit_list block_about_why_we block_about_text_mt_34 block_about_text_mt_40 about_baner last_news callback_with_map staffs products_hit_auto_slider about_us read_more products_viewed_slider products_hit_slider products_popular_slider categories_popular_slider products_sale_slider instagram')
                ->hasOne('h2')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('why_we staffs  about_us about_baner  read_more staffs block_about_text_mt_34 block_about_text_mt_40')
                ->hasOne('description')
                ->onlyForm(),

            Definition::make('Преимущества')
                ->className('advantages block_about_why_we credit_list')
                ->hasMany('advantages', BlockAdvantages::class)
                ->className('advantages'),

            Checkbox::make('Отображать', 'is_active')->default(1),
        ];
    }
}
