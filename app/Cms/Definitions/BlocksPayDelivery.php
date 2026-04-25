<?php

namespace App\Cms\Definitions;

use App\Cms\Definitions\HasManyBlocks\BlockDeliveryListText;
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

class BlocksPayDelivery extends Resource
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
                    'block_h2' => [
                        'value' => 'H2',
                        'data-img' => glide('/blocks/h2.png', ['w' => 400, 'h' => 400]),
                        'data-class' => 'navigation',
                    ],
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

                ])->action(),

            Text::make('Заголовок H2 ', 'title')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_h2 block_delivery_info delivery_list_text')
                ->hasOne('h2')
                ->onlyForm(),

            Froala::make('Описание', 'description')
                ->filter()
                ->language()
                ->sortable()
                ->className('block_delivery_info')
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

//            Froala::make('Описание', 'description')
//                ->filter()
//                ->language()
//                ->sortable()
//                ->className('contacts_with_map')
//                ->hasOne('contactsWithMap')
//                ->onlyForm(),

        ];
    }
}
