<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\ContactRubric;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockContactRubrics extends Resource
{
    public $model = ContactRubric::class;

    public $title = 'Контакты';

    protected $cacheTag = 'block_contacts_rubrics';

    protected $orderBy = 'priority asc';

    protected $isSortable = false;

    public function fields()
    {
        return [
            Id::make('#', 'id')->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Название блока', 'title')->filter()->language(),
            Image::make('Иконка', 'picture')->onlyForm(),
            Definition::make('Контакты')
                ->hasMany('contact_manies', BlockContactsMany::class),
        ];
    }
}
