<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\Account;
use App\Cms\Fields\ForeignAjaxExt;
use App\Cms\Fields\ForeignAjaxManager;
use App\Cms\Fields\ForeignAjaxUser;
use App\Cms\Fields\Invoice;
use App\Cms\Fields\LiqPayOnline;
use App\Cms\Fields\NpTtn;
use App\Cms\Fields\Order1cProcessed;
use App\Cms\Fields\OrderContactsExt;
use App\Cms\Fields\OrderDelivery;
use App\Cms\Fields\OrderID;
use App\Cms\Fields\OrderProducts as OrderProductsField;
use App\Cms\Fields\OrderStatuses;
use App\Cms\Fields\PaymentDefinition;
use App\Cms\Fields\Printing;
use App\Cms\Fields\Receipt;
use App\Cms\Fields\RequisitesEmail;
use App\Cms\Services\ActionsOrders;
use App\Cms\Services\ListingOrders;
use App\Models\Order as OrderModel;
use Carbon\Carbon;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\ReadonlyField;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;

//use Vis\Builder\Services\Actions;
////use Vis\Builder\Services\Listing;

class OrdersMini extends Orders
{
    public $model = OrderModel::class;

    public $title = 'Заказы';

    public function fields(): array
    {
        return [
            'Основное' => [
                Order1cProcessed::make('1с', 'processed_1c')->onlyForm(),
                OrderID::make('Заказ', 'id')->filter()->sortable(),
                OrderContactsExt::make('Контактная информация', 'contact')->onlyForm(),

                OrderProductsField::make('Товары')
                    ->hasMany('products', OrderProducts::class),

                Number::make('Сумма заказа без скидки, грн', 'cost_without_sale')->onlyForm(),
                Text::make('Промокод', 'promo_code')->onlyForm(),
                Text::make('Скидка промокода, %', 'sale_promo')->onlyForm(),
                Text::make('Скидка дисконтной карты, %', 'sale_discount')->onlyForm(),

                //Number::make('Скидка, %', 'sale')->onlyForm(),
                Hidden::make('Скидка итоговая, %', 'sale')->onlyForm(),
                Number::make('Наценка, грн', 'tax')->filter()->sortable(),
                Number::make('Сумма заказа, грн', 'cost')->filter()->sortable(),
                Number::make('Стоимость доставки, грн', 'price_delivery')->onlyForm(),
                Checkbox::make('Доставка оплачивается отдельно', 'is_delivery_paid_separately')->onlyForm(),

                Textarea::make('Комментарий клиента', 'comment')->onlyForm()
                    ->onlyForm(),
                Checkbox::make('Перезвоните мне', 'call_me')->onlyForm(),

                Textarea::make('Примечание менеджера', 'note_for_manager')->onlyForm(),
                Textarea::make('Комментарии из 1С', 'system_notes')->onlyForm(),

                Datetime::make('Дата создания', 'created_at')
                    ->filter()
                    ->sortable()
                    ->default(Carbon::now()),
                OrderStatuses::make('Статусы')->onlyForm(),
                //Hidden::make('Дата обновлнения', 'updated_at')
                //    ->onlyForm(),

                ReadonlyField::make('Id заказа на Prom', 'prom_id')->onlyForm(),
                ForeignAjaxManager::make('Менеджер', 'manager_id')
                    ->sortable()
                    ->filter()
                    ->options((new Options('manager'))
                        ->keyField('first_name')),
            ],

            'Данные клиента' => [

                ForeignAjaxUser::make('Клиент', 'user_id')
                    ->onlyForm()
                    ->options((new Options('user'))
                        ->keyField('first_name')),

                Text::make('Имя', 'first_name')
                    ->filter()
                    ->onlyForm()
                    ->sortable(),

                Text::make('Фамилия', 'last_name')
                    ->filter()
                    ->onlyForm()
                    ->sortable(),

                Text::make('Отчество', 'patronymic')
                    ->onlyForm(),

                Text::make('Телефон', 'phone')
                    ->filter()
                    ->onlyForm()
                    ->sortable(),

                Text::make('Email', 'email')
                    ->onlyForm(),
            ],

            'Посылку забирает' => [

                Select::make('Посылку забирает', 'receiver')->options([
                    'user' => 'Покупатель',
                    'other' => 'Другой человек',
                ])->onlyForm()->action(),

                Text::make('Имя', 'receiver_first_name')
                    ->onlyForm()->className('other'),

                Text::make('Фамилия', 'receiver_last_name')
                    ->onlyForm()->className('other'),
                Text::make('Отчество', 'receiver_patronymic_name')
                    ->onlyForm()->className('other'),

                Text::make('Телефон', 'receiver_phone')
                    ->onlyForm()->className('other'),
            ],

            'Статусы' => [

                Datetime::make('Резерв до', 'reserved_to')
                    ->filter()
                    ->sortable()
                    ->onlyForm(),

                Foreign::make('Статус заказа', 'order_status_id')
                    ->options((new Options('status'))
                        ->isJson())
                    ->filter()
                    ->onlyForm(),
                //->saveOnChange(),
                //->fastEdit(),
                Textarea::make('Причина расформирования заказа', 'cancel_reason')->onlyForm()->className('9'),

                Foreign::make('Статус комплектации', 'complect_status_id')
                    ->options((new Options('complectation'))
                        ->isJson())
                    ->filter()
                    ->onlyForm(),
                //->saveOnChange(),
                //->fastEdit(),

                Foreign::make('Статус оплаты', 'is_online_payed')
                    ->options((new Options('paymentstatus'))
                        ->isJson())
                    ->filter()
                    ->onlyForm(),
                //->saveOnChange(),
                //->fastEdit(),

                LiqPayOnline::make('LiqPayOnline оплата')->onlyForm(),
            ],

            'Доставка и оплата' => [

                NpTtn::make('Создать ЕН Новой почты')->onlyForm(),

                Text::make('Трекинг', 'tracking_num')->onlyForm(),
                //->onlyForm(),

                OrderDelivery::make('Доставка', 'delivery_id')
                    ->options((new Options('delivery'))
                        ->isJson())
                    ->filter('foreign')->action()->onlyForm(),
                //->onlyForm(),
                ForeignAjaxExt::make('Город', 'city_id')
                    ->options((new Options('city'))
                        ->isJson())
                    ->filter()->onlyForm()->className('2 6 7 8 9'),

                Textarea::make('Адрес', 'address')
                    ->onlyForm()->className('3 9'),

                ForeignAjaxExt::make('Пункт самовывоза', 'delivery_pickup_point_id')
                    ->options((new Options('deliveryPickupPoint'))
                        ->isJson())
                    ->filter()->onlyForm()->className('1'),

                ForeignAjaxExt::make('Отделение новой почты', 'np_warehouse_id')
                    ->options((new Options('npWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('2'),

                ForeignAjaxExt::make('Отделение укрпочты', 'ukrposhta_warehouse_id')
                    ->options((new Options('ukrposhtaWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('6'),

                ForeignAjaxExt::make('Отделение justin', 'justin_warehouse_id')
                    ->options((new Options('justinWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('7'),

                ForeignAjaxExt::make('Отделение meest', 'meest_warehouse_id')
                    ->options((new Options('meestWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('8'),

                Foreign::make('Метод оплаты', 'pay_method_id')
                    ->options((new Options('payMethod'))
                        ->isJson())
                    ->filter()->onlyForm(),

            ],

            'Платежи' => [
                PaymentDefinition::make('Платежи')
                    ->hasMany('payments', OrderPayments::class),
            ],

            'UTM-метки' => [
                ReadonlyField::make('UTM-CAMPAIGN', 'utm_campaign')
                    ->hasOne('orderUtm')->onlyForm(),
                ReadonlyField::make('UTM-CONTENT', 'utm_content')
                    ->hasOne('orderUtm')->onlyForm(),
                ReadonlyField::make('UTM-MEDIUM', 'utm_medium')
                    ->hasOne('orderUtm')->onlyForm(),
                ReadonlyField::make('UTM-SOURCE', 'utm_source')
                    ->hasOne('orderUtm')->onlyForm(),
                ReadonlyField::make('UTM-TERM', 'utm_term')
                    ->hasOne('orderUtm')->onlyForm(),
            ],

            'Документы' => [
                Foreign::make('Юридична особа для замовлення', 'legal_entities_recipient_id')
                    ->nullable('Выбрать')
                    ->saveOnChange()
                    ->options((new Options('recipient'))
                        ->isJson())
                    ->onlyForm(),
                Receipt::make('Фискализировать чек')->onlyForm(),
                Printing::make('Печать')->onlyForm(),
                Invoice::make('Рахунок ФОП')->onlyForm(),
                Account::make('Накладна')->onlyForm(),
                RequisitesEmail::make('Реквизиты на email')->onlyForm(),
            ],
        ];
    }

    //Добавить действие для откр. в новом окне
    public function actions()
    {
        return ActionsOrders::make()->orderopen()->insert()->update()->revisions()->delete();
    }

    public function getList()
    {
        $list = new ListingOrders($this);
        $listingRecords = $list->body();

        return view('cms.fields.ordertable', compact('list', 'listingRecords'));
    }
}
