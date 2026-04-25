<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\Contact;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockContacts extends Resource
{
    public $model = Contact::class;

    public $title = 'Блок в контактах';

    protected $cacheTag = 'block_contacts';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->filter()->language(),
            Text::make('Описание', 'short_description')->filter()->language(),
            Image::make('Картинка', 'picture')->onlyForm(),
        ];
    }
}
