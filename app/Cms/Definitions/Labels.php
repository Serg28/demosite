<?php

namespace App\Cms\Definitions;

use App\Models\Label;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Color;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;

class Labels extends Resource
{
    public $model = Label::class;

    public $title = 'Метки';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Название', 'title')->filter()->sortable()->language(),
            Color::make('Цвет', 'color')->filter()->sortable(),
        ];
    }
}
