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

class Blocks extends Resource
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
//                    'block_about_text_mt_34' => [
//                        'value' => 'Блок текста 34, на странице О нас',
//                        'data-img' => glide('/blocks/block_about_text-mt-34', ['w' => 400, 'h' => 400]),
//                        'data-class' => 'block_about_text_mt_34'
//                    ],
//                    'block_about_text_mt_40' => [
//                        'value' => 'Блок текста 40, на странице О нас',
//                        'data-img' => glide('/blocks/block_about_text-mt-40', ['w' => 400, 'h' => 400]),
//                        'data-class' => 'block_about_text_mt_40'
//                    ],
//                    'block_about_why_we' => [
//                        'value' => 'Переваги які ми пропонуємо, на странице О нас',
//                        'data-img' => glide('/blocks/block_about_why_we.png', ['w' => 400, 'h' => 400]),
//                        'data-class' => 'block_about_why_we'
//                    ],

                    'block_h2' => [
                        'value' => 'H2',
                        'data-img' => glide('/blocks/h2.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'navigation',
                    ],

                    /*'contacts' => [
                        'value' => 'Контакты (однин блок = одно значение)',
                        'data-img' => glide('/blocks/block_contact.png', ['w' => 400, 'h' => 400]),
                        //'data-class' => 'contacts'
                        'data-class' => 'contacts'
                    ],*/

                    'contact_rubrics' => [
                        'value' => 'Контакты (один блок = много значений)',
                        'data-img' => glide('/blocks/block_contacts_multi.png', ['w' => 400, 'h' => 400]),
                        //'data-class' => 'contacts'
                        'data-class' => 'contact_rubrics',
                    ],

                    /*'callback_with_map' => [
                        'value' => 'Обратная связь с картой',
                        'data-img' => glide('/blocks/block_callback_with_map.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'contacts'
                    ],*/

                    //--
                    'contacts_with_map' => [
                        'value' => 'Карта с текстом слева',
                        'data-img' => glide('/blocks/block_contacts_withmap_lefttext.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'contacts',
                    ],

                    'contact_lines_with_map' => [
                        'value' => 'Карта + контакты слева',
                        'data-img' => glide('/blocks/block_contactsleft_map.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'contacts',
                    ],

                    'pricelists' => [
                        'value' => 'Прайс-лист',
                        'data-img' => glide('/blocks/block_pricelist.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'pricelist',
                    ],
                    //--

                    /*'faq_rubrics' => [
                        'value' => 'FAQ',
                        'data-img' => glide('/blocks/block_faq.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'faq_rubrics'
                    ],*/

                    'last_news' => [
                        'value' => 'Последние новости',
                        'data-img' => glide('/blocks/block_last_news.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'last_news',
                    ],

                    /*'advantages' => [
                        'value' => 'Преимущества',
                        'data-img' => glide('/blocks/block_advantage.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'advantages'
                    ],

                    'reviews' => [
                        'value' => 'Отзывы',
                        'data-img' => glide('/blocks/block_review.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'reviews'
                    ],

                    'why_we' => [
                        'value' => 'Почему именно мы',
                        'data-img' => glide('/blocks/block_why_we.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'why_we'
                    ],*/

                    'staffs' => [
                        'value' => 'Наша команда ',
                        'data-img' => glide('/blocks/block_staffs.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'staffs'
                    ],
/*
                    'about_us' => [
                        'value' => 'Большое изображение и текст справа',
                        'data-img' => glide('/blocks/block_about_us.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'about'
                    ],*/

                    'banner_slider' => [
                        'value' => 'Слайдер баннеров',
                        'data-img' => glide('/blocks/block_banner_slider.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders',
                    ],
                    /*
                    'read_more' => [
                        'value' => 'Читать больше',
                        'data-img' => glide('/blocks/block_read_more.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'about'
                    ],

                    'client_logo' => [
                        'value' => 'Логотип клиентов',
                        'data-img' => glide('/blocks/block_client_logo.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders'
                    ],*/

                    'products_hit_slider' => [
                        'value' => 'Слайдер топ продуктов (выбор вручную SELECT)', //товары вручную указываются
                        'data-img' => glide('/blocks/block_hits_products.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders',
                    ],

                    'products_hit_auto_slider' => [
                        'value' => 'Слайдер топ продаж (автоматически)', //товары вручную указываются
                        'data-img' => glide('/blocks/block_hits_products.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders',
                    ],

                    'products_popular_slider' => [
                        'value' => 'Слайдер продуктов (выбор вручную SELECT)',
                        'data-img' => glide('/blocks/block_products_popular_slider.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders',
                    ],

                    'products_sale_slider' => [
                        'value' => 'Слайдер распродаж (артикулы через запятую), лимит 15 шт.',
                        'data-img' => glide('/blocks/block_products_popular_slider.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders',
                    ],

                    /*'categories_popular_slider' => [
                        'value' => 'Слайдер популярных категорий (выбор вручную)',
                        'data-img' => glide('/blocks/block_categories_popular_slider.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders'
                    ],*/

                    'products_viewed_slider' => [
                        'value' => 'Слайдер Вы смотрели',
                        'data-img' => glide('/blocks/block_product_viewed.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders',
                    ],

                    /*'instagram' => [
                        'value' => 'Блок Instagram',
                        'data-img' => glide('/blocks/block_instagram.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'sliders'
                    ],*/

                ])->action(),

            Text::make('Заголовок H2 ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_h2 reviews pricelists block_about_why_we block_about_text_mt_34 block_about_text_mt_40 about_baner last_news callback_with_map staffs products_hit_auto_slider about_us read_more products_viewed_slider products_hit_slider products_popular_slider categories_popular_slider products_sale_slider instagram')
                ->hasOne('h2')
                ->onlyForm(),

            Text::make('Карта ', 'map')
                ->filter()
                ->language()
                ->sortable()
                ->className('callback_with_map contact_lines_with_map')
                ->hasOne('callbackWithMap')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('why_we staffs  about_us about_baner  read_more staffs block_about_text_mt_34 block_about_text_mt_40')
                ->hasOne('description')
                ->onlyForm(),

            Image::make('Картинка', 'picture')
                ->className('about_us about_baner contacts')
                ->language()
                ->hasOne('picture')
                ->onlyForm(),

            Definition::make('Блоки в контактах')
                ->hasMany('contacts', BlockContacts::class)
                ->className('contacts'),

            //
            Definition::make('Блоки контактов')
                ->hasMany('contactRubric', BlockContactRubrics::class)
                ->className('contact_rubrics contact_lines_with_map'),
            //

            Definition::make('FAQ')
                ->hasMany('faqRubrics', BlockFaqRubrics::class)
                ->className('faq_rubrics'),

            Definition::make('Преимущества')
                ->className('advantages block_about_why_we')
                ->hasMany('advantages', BlockAdvantages::class)
                ->className('advantages'),

            Definition::make('Отзывы')
                ->hasMany('reviews', Review::class)
                ->className('reviews'),

            Definition::make('Блок почему именно мы')
                ->hasMany('whyWe', BlockWhyWe::class)
                ->className('why_we'),

            Definition::make('Персонал')
                ->hasMany('staffs', Staff::class)
                ->className('staffs'),

            Definition::make('Слайдер')
                ->hasMany('bannersSlider', BannersSlider::class)
                ->className('banner_slider'),

            Definition::make('Лого')
                ->hasMany('logos', ClientsLogo::class)
                ->className('client_logo'),

            ManyToManyAjaxProducts::make('Выбор топ продуктов')->options(
                (new Options('hitProducts'))->isJson()
            )->className('products_hit_slider'),

            ManyToManyAjaxProducts::make('Выбор популярных продуктов')->options(
                (new Options('popularProducts'))->isJson()
            )->className('products_popular_slider'),

            ManyToMany::make('Выбор популярных категорий')->options(
                (new Options('popularCategories'))->keyField('title')
            )->className('categories_popular_slider'),

            ProductsCodes::make('Список артикулов через запятую')->options(
                (new Options('saleProducts'))->isJson()
            )->className('products_sale_slider'),

            Checkbox::make('Отображать', 'is_active')->default(1),

            //--- Блок Карта с текстом слева
            Text::make('Заголовок ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('contacts_with_map')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Froala::make('Адрес', 'adress')
                ->filter()
                ->language()
                ->sortable()
                ->className('contacts_with_map')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('contacts_with_map')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Text::make('Карта ', 'map')
                ->filter()
                ->language()
                ->sortable()
                ->className('contacts_with_map')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Definition::make('Время работы')
                ->hasMany('worktime', BlockWorkTime::class)
                ->className('contacts_with_map'),
            //----

            //--- Блок Прайс-лист
            Definition::make('Категория прайс-листа (напр., Рама)')
                ->hasMany('pricelistTitle', BlockPricelistTitle::class)
                ->sortable()
                ->className('pricelists'),
        ];
    }
}
