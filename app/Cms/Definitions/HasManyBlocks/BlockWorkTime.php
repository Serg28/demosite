<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\WorkTime;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockWorkTime extends Resource
{
    public $model = WorkTime::class;

    public $title = 'РАсписание работы';

    protected $cacheTag = 'block_worktime';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Дни', 'title')->filter()->language(),
            Text::make('Время', 'short_description')->filter()->language(),
            Image::make('Картинка', 'picture')->onlyForm(),
        ];
    }
}
