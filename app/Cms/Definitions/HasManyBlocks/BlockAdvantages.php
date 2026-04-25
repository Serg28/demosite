<?php

namespace App\Cms\Definitions\HasManyBlocks;

use App\Models\Blocks\Advantage;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class BlockAdvantages extends Resource
{
    public $model = Advantage::class;

    public $title = 'Блок преимущества';

    protected $cacheTag = 'block_advantages';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable()->onlyForm(),
            Hidden::make('block_id', 'block_id')->onlyForm()->default(request('foreign_field_id')),
            Text::make('Заголовок', 'title')->filter()->language(),
            Image::make('Картинка', 'picture')->onlyForm(),
        ];
    }
}
