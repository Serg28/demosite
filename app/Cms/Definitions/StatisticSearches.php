<?php

namespace App\Cms\Definitions;

use App\Models\StatisticSearch;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Export;

class StatisticSearches extends Resource
{
    public $model = StatisticSearch::class;

    public $title = 'StatisticSearch';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'test' => [
                Id::make('#', 'id')->sortable(),
                Text::make('query', 'query')->filter()->sortable(),
                Text::make('user_id', 'user_id')->filter()->sortable(),

                Datetime::make('Дата создания', 'created_at')->filter()->sortable(),
            ],
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->clone()->delete();
    }

    public function buttons()
    {
        return [
            Export::class,
        ];
    }
}
