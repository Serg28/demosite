<?php

namespace App\Cms\Definitions;

use App\Models\PayMethod;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\ManyToMany;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Relations\Options;
use Linecore\Cms\Fields\Select;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Services\Actions;

class PayMethods extends Resource
{
    public $model = PayMethod::class;

    public $title = 'Способи оплати';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            'Основне' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Назва', 'title')->filter()->sortable()->language(),
                Text::make('Slug', 'slug')->onlyForm(),
                Select::make('Gateway', 'gateway')
                    ->options(array_combine(
                        array_keys(config('payment.gateways', [])),
                        array_keys(config('payment.gateways', [])),
                    ))
                    ->onlyForm(),
                Number::make('Комісія %', 'commission_percent')->default(0)->onlyForm(),
                Checkbox::make('Активний', 'is_active')->filter()->sortable(),
            ],
            'Налаштування платежу' => [
                Checkbox::make('Холд (захоплення коштів)', 'use_hold')
                    ->onlyForm()
                    ->comment('Якщо увімкнено — кошти спочатку блокуються (hold), потім менеджер вручну підтверджує зняття. Якщо вимкнено — списання відбувається одразу.'),
            ],
            'Доставка' => [
                ManyToMany::make('Доступні способи доставки')
                    ->options(new Options('deliveries'))
                    ->className('col-md-12'),
            ],
        ];
    }

    public function actions(): Actions
    {
        return Actions::make($this)->insert()->update()->delete();
    }
}
