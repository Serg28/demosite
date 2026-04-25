<?php

namespace App\Cms\Definitions;

use App\Models\Subscription as SubscriptionModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Export;

class Subscriptions extends Resource
{
    public $model = SubscriptionModel::class;

    public $title = 'Подписки';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Email', 'email')->filter()->sortable(),

            Datetime::make('Дата создания', 'created_at')->filter()->sortable(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }

    public function buttons()
    {
        return [
            Export::class,
        ];
    }
}
