<?php

namespace App\Cms\Definitions;

use App\Models\Article;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToManyAjax;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Articles extends Resource
{
    public $model = Article::class;

    public $title = 'Статьи';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Картинки111', 'title'),
                Image::make('Картинки111', 'picture'),
                Checkbox::make('Checkbox', 'checkbox')->filter(),
                Datetime::make('Datetime', 'created_at')->filter()->sortable(),
                ManyToManyAjax::make('Статьи')
                    ->options(
                        (new Options('trees'))
                            ->where('parent_id', '=', '1')
                            ->orderBy('created_at', 'asc')
                    ),
                ForeignAjax::make('Дерево', 'tree_id')
                    ->filter()
                    ->options(
                        (new Options('trees2'))
                            ->where('parent_id', '=', '1')
                            ->orderBy('created_at', 'asc')
                    ),
                //  Definition::make('Новости')->hasMany('news', News::class)
            ],
            'test2' => [
            ],

        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }
}
