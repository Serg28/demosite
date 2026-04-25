<?php

namespace App\Cms\Definitions;

use App\Models\SliderMain;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class SliderMains extends Resource
{
    public $model = SliderMain::class;

    public $title = 'Слайдер на главной';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Заголовок', 'title')->filter()->sortable()->language(),
            Froala::make('Описание', 'description')->onlyForm()->language(),
            Image::make('Картинка', 'picture')->filter()->sortable()->language(),
            Text::make('Ссылка', 'link')->onlyForm()->language(),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
            Checkbox::make('Открывать в новом окне', 'is_target_blank')->onlyForm(),
        ];
    }
}
