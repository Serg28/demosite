<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\ClientLogo;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;

class ClientsLogo extends Resource
{
    public $model = ClientLogo::class;

    public $title = 'Лого';

    protected $cacheTag = 'block_clients_logo';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Image::make('Картинка', 'picture')->filter()->sortable(),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
        ];
    }
}
