<?php

namespace App\Cms\Definitions;

use App\Models\OrderPayment;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Services\Actions;

class OrderPayments extends Resource
{
    public $model = OrderPayment::class;

    public $title = 'Платежи';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),

            Hidden::make('Заказ', 'order_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),

            Select::make('Тип', 'type')->filter()->sortable()->options($this->model()->types()),

            Foreign::make('Юр. лицо получателя', 'legal_entities_recipient_id')
                ->nullable('Выбрать')
                ->options((new Options('legalEntitiesRecipient'))
                    ->isJson())
                ->filter()->sortable(),
            Number::make('Сумма, грн', 'price')->filter()->sortable(),
            Datetime::make('Дата', 'created_at')->filter()->sortable()->default(Carbon::now()),
            Checkbox::make('Оплачено', 'is_payed')->filter()->sortable(),
        ];
    }

    public function actions(): Actions
    {
        return Actions::make()->insert()->update()->preview()->clone()->delete();
    }
}
