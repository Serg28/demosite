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

class BlocksContacts extends Resource
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

                ])->action(),

            Text::make('Карта ', 'map')
                ->filter()
                ->language()
                ->sortable()
                ->className('callback_with_map contact_lines_with_map')
                ->hasOne('callbackWithMap')
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
        ];
    }
}
