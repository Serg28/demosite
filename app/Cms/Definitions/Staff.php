<?php

namespace App\Cms\Definitions;

use App\Models\Staff as StaffModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;

class Staff extends Resource
{
    public $model = StaffModel::class;

    public $title = 'Персонал';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Имя и фамилия', 'title')->filter()->sortable()->language(),
            Textarea::make('Должность', 'profession')->language(),
            Image::make('Фото', 'picture'),
            Checkbox::make('Активно', 'is_active')->filter()->sortable(),
        ];
    }
}
