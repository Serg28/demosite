<?php

namespace App\Cms\Definitions;

use App\Models\Review as ReviewModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class Review extends Resource
{
    public $model = ReviewModel::class;

    public $title = 'Отзывы';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Имя и фамилия', 'title')->filter()->sortable()->language(),
            Text::make('Отзыв', 'description')->language(),
            Text::make('Должность', 'proffection')->language()->onlyForm(),
            Image::make('Фото', 'picture')->onlyForm(),
            Checkbox::make('Активно', 'is_active')->sortable(),
        ];
    }
}
