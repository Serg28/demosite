<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\ContactMany;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockContactsMany extends Resource
{
    public $model = ContactMany::class;

    public $title = 'Контакты';

    protected $cacheTag = 'contacts_many';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            /* Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->filter()->language(),
            Text::make('Описание', 'short_description')->filter()->language(),
            Image::make('Картинка', 'picture')->onlyForm()*/
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('contact_rubric_id', 'contact_rubric_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Название контакта (напр., Телефон)', 'title')->filter()->language(),
            Text::make('Значение', 'description')->filter()->language(),
            Froala::make('Текстовая информация', 'text')->filter()->language()->onlyForm(),
            Image::make('Картинка', 'picture')->onlyForm(),
        ];
    }
}
