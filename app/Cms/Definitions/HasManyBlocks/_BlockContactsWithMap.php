<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\ContactsWithMap;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockContactsWithMap extends Resource
{
    public $model = ContactsWithMap::class;

    public $title = 'Карта с текстом слева';

    protected $cacheTag = 'block_contacts_with_map';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->filter()->language(),
            Text::make('Текстовая информация', 'description')->filter()->language(),
            Text::make('Карта', 'map')->filter()->language(),
            Image::make('Картинка', 'picture')->onlyForm(),
            Definition::make('Время работы')
                ->hasMany('worktime', BlockWorkTime::class),
        ];
    }
}
