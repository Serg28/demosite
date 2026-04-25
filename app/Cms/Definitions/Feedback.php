<?php

namespace App\Cms\Definitions;

use App\Models\Feedback as FeedbackModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Export;

class Feedback extends Resource
{
    public $model = FeedbackModel::class;

    public $title = 'Обратная связь';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('ФИО', 'name')->filter()->sortable(),
            Text::make('Телефон', 'phone')->filter()->sortable(),
            //     Text::make('Email', 'email')->filter()->sortable(),
            Textarea::make('Сообщение', 'comment')->filter()->sortable(),
            Datetime::make('Дата создания', 'created_at')->filter()->sortable(),
        ];
    }

    public function actions()
    {
        return Actions::make()->update()->delete();
    }

    public function buttons()
    {
        return [
            Export::class,
        ];
    }
}
