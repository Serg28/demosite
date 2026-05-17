<?php

namespace App\Cms\Definitions;

use App\Models\Order;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Datetime;
use Linecore\Cms\Fields\Definition;
use Linecore\Cms\Fields\Foreign;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\ReadonlyField;
use Linecore\Cms\Fields\Relations\Options;
use Linecore\Cms\Fields\Select;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Fields\Textarea;
use Linecore\Cms\Services\Actions;
use Linecore\Cms\Services\Export;

class Orders extends Resource
{
    public $model = Order::class;

    public $title = 'Замовлення';

    protected $relations = [
        'delivery',
        'payMethod',
        'city',
        'deliveryWarehouse',
        'deliveryPickupPoint',
        'user',
    ];

    public function fields(): array
    {
        return [
            'Основне' => [
                Id::make('#', 'id')->filter()->sortable(),
                Text::make('Ім\'я', 'first_name')->filter()->sortable(),
                Text::make('Прізвище', 'last_name')->filter()->sortable(),
                Text::make('Телефон', 'phone')->filter()->sortable(),
                Text::make('Email', 'email')->filter()->sortable(),
                Number::make('Сума замовлення, грн', 'cost')->filter()->sortable(),
                Number::make('Сума без знижки, грн', 'cost_without_sale')->onlyForm(),
                Number::make('Знижка, грн', 'sale')->onlyForm(),
                Number::make('Знижка промокоду, грн', 'sale_promo')->onlyForm(),
                Number::make('Знижка картки, грн', 'sale_discount')->onlyForm(),
                Number::make('Комісія оплати, грн', 'payment_commission')->onlyForm(),
                Number::make('Вартість доставки, грн', 'price_delivery')->onlyForm(),
                Select::make('Статус', 'status')->options([
                    'pending'    => 'Очікує',
                    'processing' => 'В обробці',
                    'completed'  => 'Виконано',
                    'cancelled'  => 'Скасовано',
                ])->filter()->sortable()->fastEdit(),
                Textarea::make('Коментар клієнта', 'comment')->onlyForm(),
                Checkbox::make('Передзвоніть мені', 'call_me')->onlyForm(),
                Datetime::make('Дата створення', 'created_at')->filter()->sortable(),
            ],

            'Дані клієнта' => [
                ReadonlyField::make('Клієнт (user_id)', 'user_id')->onlyForm(),
            ],

            'Отримувач' => [
                Text::make('Ім\'я отримувача', 'receiver_first_name')->onlyForm(),
                Text::make('Прізвище отримувача', 'receiver_last_name')->onlyForm(),
                Text::make('По-батькові отримувача', 'receiver_patronymic')->onlyForm(),
                Text::make('Телефон отримувача', 'receiver_phone')->onlyForm(),
                Text::make('Email отримувача', 'receiver_email')->onlyForm(),
            ],

            'Доставка та оплата' => [
                Text::make('Адреса', 'address')->onlyForm(),
                Foreign::make('Доставка', 'delivery_id')
                    ->options((new Options('delivery'))->isJson())
                    ->filter('foreign')->onlyForm(),
                Foreign::make('Місто', 'city_id')
                    ->options((new Options('city'))->isJson())
                    ->filter()->onlyForm(),
                Foreign::make('Відділення', 'delivery_warehouse_id')
                    ->options((new Options('deliveryWarehouse'))->isJson())
                    ->onlyForm(),
                Foreign::make('Спосіб оплати', 'pay_method_id')
                    ->options((new Options('payMethod'))->isJson())
                    ->filter('foreign')->onlyForm(),
            ],

            'Товари' => [
                Definition::make('Товари')
                    ->hasMany('products', OrderProducts::class),
            ],
        ];
    }

    public function buttons(): array
    {
        return [
            Export::class,
        ];
    }

    public function actions(): Actions
    {
        return Actions::make($this)->update()->delete();
    }
}
