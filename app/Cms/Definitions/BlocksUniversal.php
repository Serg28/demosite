<?php

namespace App\Cms\Definitions;

use App\Cms\Definitions\HasManyBlocks\BlockAdvantages;
use App\Cms\Definitions\HasManyBlocks\BlockContactRubrics;
use App\Cms\Definitions\HasManyBlocks\BlockDeliveryListText;
use App\Cms\Definitions\HasManyBlocks\BlockWorkTime;
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

class BlocksUniversal extends Resource
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
//                    'block_h2' => [
//                        'value' => 'H2',
//                        'data-img' => glide('/blocks/h2.png', ['w' => 400, 'h' => 400]),
//                        'data-class' => 'navigation',
//                    ],
                    //-- Pay Delivery
                    'block_delivery_info' => [
                        'value' => 'Блок Info',
                        'data-img' => glide('/blocks/block_delivery_info.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'block_delivery_info',
                    ],
                    'delivery_list_text' => [
                        'value' => 'Текстовый блок (заголовок, иконка, много строк)',
                        'data-img' => glide('/blocks/block_delivery_list_text.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'delivery_list_text',
                    ],
                    //-- Contact
//                    'contact_rubrics' => [
//                        'value' => 'Контакты (один блок = много значений)',
//                        'data-img' => glide('/blocks/block_contacts_multi.png', ['w' => 400, 'h' => 400]),
//                        //'data-class' => 'contacts'
//                        'data-class' => 'contact_rubrics',
//                    ],
//                    'contacts_with_map' => [
//                        'value' => 'Карта с текстом слева',
//                        'data-img' => glide('/blocks/block_contacts_withmap_lefttext.png', ['w' => 400, 'h' => 400]),
//                        'data-class' => 'contacts',
//                    ],
//                    'contact_lines_with_map' => [
//                        'value' => 'Карта + контакты слева',
//                        'data-img' => glide('/blocks/block_contactsleft_map.png', ['w' => 400, 'h' => 400]),
//                        'data-class' => 'contacts',
//                    ],
                    //--
                    //-- About
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
                    //--
                    //Credit
                    'block_advantages_page_credit' => [
                        'value' => 'Блок описания',
                        'data-img' => glide('/blocks/block_advantages_page_credit', ['w' => 400, 'h' => 400]),
                        'data-class' => 'block_advantages_page_credit'
                    ],
                ])->action(),

            Text::make('Заголовок H2 ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_h2 block_delivery_info delivery_list_text about_baner block_about_text_mt_34 block_about_text_mt_40 block_about_why_we block_advantages_page_credit')
                ->hasOne('h2')
                ->onlyForm(),

            Froala::make('Малое Описание', 'short_description')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_advantages_page_credit')
                ->hasOne('short_description')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_delivery_info about_baner block_about_text_mt_34 block_about_text_mt_40 block_advantages_page_credit')
                ->hasOne('description')
                ->onlyForm(),

            Image::make('Картинка', 'picture')
                ->className('block_delivery_info delivery_list_text')
                ->language()
                ->hasOne('picture')
                ->onlyForm(),

//            Definition::make('Блоки в контактах')
//                ->hasMany('contacts', BlockContacts::class)
//                ->className('contacts'),

            //
            Definition::make('Блоки текстовых полей с заголовком')
                ->hasMany('delivery_list_text', BlockDeliveryListText::class)
                ->className('delivery_list_text'),
            //

            Checkbox::make('Отображать', 'is_active')->default(1)->className('block_delivery_info delivery_list_text'),

            //Contact
            Definition::make('Блоки контактов')
                ->hasMany('contactRubric', BlockContactRubrics::class)
                ->className('contact_rubrics contact_lines_with_map'),

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
                ->className('contacts_with_map contact_lines_with_map')
                ->hasOne('contactsWithMap')
                ->onlyForm(),

            Definition::make('Время работы')
                ->hasMany('worktime', BlockWorkTime::class)
                ->className('contacts_with_map'),
            //
//            Froala::make('Описание', 'description')
//                ->filter()
//                ->language()
//                ->sortable()
//                ->className('contacts_with_map')
//                ->hasOne('contactsWithMap')
//                ->onlyForm(),
        //About
            Definition::make('Преимущества')
                ->className('advantages block_about_why_we credit_list')
                ->hasMany('advantages', BlockAdvantages::class)
                ->className('advantages'),

        ];

    }
}
