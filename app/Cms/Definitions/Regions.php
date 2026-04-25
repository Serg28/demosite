<?php

namespace App\Cms\Definitions;

use App\Models\Region;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Regions extends Resource
{
    public $model = Region::class;

    public $title = 'Регион';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
        ];
    }
}
