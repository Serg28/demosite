<?php

namespace App\Cms\Definitions;

use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Revision;
use Vis\Builder\Services\Actions;

class Revisions extends Resource
{
    public $model = Revision::class;

    public $title = 'Контроль изменений';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Модель', 'revisionable_type')->filter(),
            Text::make('Id записи', 'revisionable_id')->filter(),
            ForeignAjax::make('Пользователь', 'user_id')
                ->options((new Options('user'))->keyField('first_name'))
                ->filter(),
            Text::make('Поле', 'key')->filter(),
            Froala::make('Старое значение', 'old_value')->filter(),
            Froala::make('Новое значение', 'new_value')->filter(),
            Datetime::make('Дата/Время', 'created_at')->filter(),
        ];
    }

    public function actions()
    {
        return Actions::make();
    }
}
