<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\BannerSlider;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Color;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BannersSlider extends Resource
{
    public $model = BannerSlider::class;

    public $title = 'Слайдер';

    protected $cacheTag = 'block_banners_slider';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->filter()->sortable()->language(),
            Text::make('Под заголовок', 'title_dop')->filter()->sortable()->language(),
            Froala::make('Описание', 'description')->onlyForm()->language(),
            Froala::make('Описание Дополнительное', 'description_dop')->onlyForm()->language(),
            Image::make('Картинка', 'picture')->filter()->sortable()->language(),
            Image::make('Картинка мобильная', 'picture_mobile')->filter()->sortable()->language(),
            Text::make('Текст на ссылке', 'link_title')->onlyForm()->language(),
            Text::make('Ссылка', 'link')->onlyForm()->language(),
            Color::make('Цвет текста и кнопки', 'color')->onlyForm(),
            Checkbox::make('Отображать кнопку', 'is_show_btn')->onlyForm(),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
            Checkbox::make('Открывать в новом окне', 'is_target_blank')->onlyForm(),
        ];
    }
}
