<?php

namespace App\Cms\Definitions;

use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Select;
use Vis\Builder\Models\Language;
use Vis\Builder\Services\Actions;

class Languages extends Resource
{
    public $model = Language::class;

    public $title = 'Языки сайта';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        $this->isSortable = \Sentinel::inRole('admin');

        return [
            Select::make('Язык', 'language')
                ->options($this->model()->supportedLocales()),
            Checkbox::make('Активен', 'is_active')->fastEdit(\Sentinel::inRole('admin')),
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->hideActions();
    }
}
