<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\ContactRubric;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockDeliveryListText extends Resource
{
    public $model = ContactRubric::class;

    public $title = 'Блок Список полей';

    protected $cacheTag = 'delivery_list_text';

    protected $orderBy = 'priority asc';

    protected $isSortable = false;

    public function fields()
    {
        return [
            Id::make('#', 'id')->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->filter()->language(),
            Froala::make('Описание', 'description')->filter()->language(),//->onlyForm()
            //Image::make('Иконка', 'picture')->onlyForm(),
        ];
    }
}
