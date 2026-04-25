<?php

namespace App\Cms\Definitions;

use App\Models\InformationBoard;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Color;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Listing;

class InformationsBoard extends Resource
{
    public $model = InformationBoard::class;

    public $title = 'Информационная полоса';

    protected $cacheTag = 'information_board';

    public function fields()
    {
        return [
            Hidden::make('#', 'id'),
            Text::make('Заголовок', 'title')->language(),
            Froala::make('Текст', 'text')->language()->onlyForm(),
           // Text::make('tooltip', 'tooltip')->language()->onlyForm(),
           // Text::make('Ссылка', 'link')->language()->filter()->sortable()->onlyForm(),
            //Image::make('Фото', 'picture')->filter()->sortable()->onlyForm(),
            //Image::make('Изображение/иконка', 'icon')->filter()->sortable()->onlyForm(),
            Image::make('Фон', 'background')->filter()->sortable(),
            Color::make('Цвет Фона', 'color')->onlyForm(),
            Checkbox::make('Отображать кнопку закрыть', 'is_show_btn')->onlyForm(),
            Text::make('style', 'style')->language()->onlyForm(),
            Text::make('class', 'className')->language()->onlyForm(),
            Number::make('Порядок сортировки', 'priority')->filter()->sortable(),
            Checkbox::make('Отображать', 'is_active'),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }

}
