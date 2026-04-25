<?php

namespace App\Cms\Definitions;

use App\Models\Settlement;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Settlements extends Resource
{
    public $model = Settlement::class;

    public $title = 'Тип населенного пункта';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
        ];
    }
}
