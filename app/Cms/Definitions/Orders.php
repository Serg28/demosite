<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\Account;
use App\Cms\Fields\ForeignAjaxExt;
use App\Cms\Fields\ForeignAjaxManager;
use App\Cms\Fields\ForeignAjaxNPWarehouses;
use App\Cms\Fields\ForeignAjaxUser;
use App\Cms\Fields\ForeignAjaxWarehouses;
use App\Cms\Fields\ForeignFOP;
use App\Cms\Fields\Invoice;
use App\Cms\Fields\LiqPayOnline;
use App\Cms\Fields\LogsForOrders;
use App\Cms\Fields\MonoPayParts;
use App\Cms\Fields\NpTtn;
use App\Cms\Fields\Order1cProcessed;
use App\Cms\Fields\OrderAdminComment;
use App\Cms\Fields\OrderChangedEmail;
use App\Cms\Fields\OrderCity;
use App\Cms\Fields\OrderContactsExt;
use App\Cms\Fields\OrderDelivery;
use App\Cms\Fields\OrderID;
use App\Cms\Fields\OrderNum;
use App\Cms\Fields\OrderProducts as OrderProductsField;
use App\Cms\Fields\OrderStatuses;
use App\Cms\Fields\PaymentDefinition;
use App\Cms\Fields\Printing;
use App\Cms\Fields\Receipt;
use App\Cms\Fields\RequisitesEmail;
use App\Cms\Fields\PayMethods;
use App\Cms\Fields\RequisitesSMS;
use App\Cms\Fields\TextExt;
use App\Cms\Services\ActionsOrders;
use App\Cms\Services\ListingOrders;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Vis\Builder\Definitions\Resource;
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
use Vis\Builder\Services\Export;

class Orders extends Resource
{
    public $model = Order::class;

    public $title = 'Заказы';

    protected $relations = ['paymentstatus','status', 'delivery','deliveryPickupPoint', 'npWarehouse', 'manager', 'complectation'];

    public function fields(): array
    {
        return [
            'Основное' => [
                Order1cProcessed::make('1с', 'processed_1c')->filter()->sortable(),
                //Id::make('Заказ', 'id')->filter()->sortable(),
                //OrderID::make('Заказ', 'id')->filter()->sortable(),
                OrderNum::make('Заказ', 'num')->filter()->sortable(),
                OrderContactsExt::make('Контактная информация', 'contact')->filter(),

                OrderProductsField::make('Товары')
                    ->hasMany('products', OrderProducts::class),

                Number::make('Сумма заказа без скидки, грн', 'cost_without_sale')->onlyForm(),
                Text::make('Промокод', 'promo_code')->onlyForm(),
                Text::make('Скидка промокода, %', 'sale_promo')->onlyForm(),
                Text::make('Скидка дисконтной карты, %', 'sale_discount')->onlyForm(),
                Hidden::make('Скидка итоговая, %', 'sale')->onlyForm(),
                Number::make('Наценка, грн', 'tax')->filter()->sortable(),
                Number::make('Сумма заказа, грн', 'cost')->filter()->sortable(),
                Number::make('Стоимость доставки, грн', 'price_delivery')->onlyForm(),
                Checkbox::make('Доставка оплачивается отдельно', 'is_delivery_paid_separately')->onlyForm(),

                Textarea::make('Комментарий клиента', 'comment')
                    ->onlyForm(),
                Checkbox::make('Перезвоните мне', 'call_me')->onlyForm(),

                OrderAdminComment::make('Примечание менеджера', 'note_for_manager')->onlyForm(),
                Textarea::make('Комментарии из 1С', 'system_notes')->onlyForm(),

                Datetime::make('Дата создания', 'created_at')
                    ->filter()
                    ->sortable()
                    ->default(Carbon::now()),
                OrderStatuses::make('Статусы')->filter(),
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

                TextExt::make('Имя', 'first_name')
                    ->saveOnChange()
                    ->filter()
                    ->onlyForm()
                    ->sortable(),

                TextExt::make('Фамилия', 'last_name')
                    ->saveOnChange()
                    ->filter()
                    ->onlyForm()
                    ->sortable(),

                TextExt::make('Отчество', 'patronymic')
                    ->saveOnChange()
                    ->onlyForm(),

                TextExt::make('Телефон', 'phone')
                    ->saveOnChange()
                    ->filter()
                    ->onlyForm()
                    ->sortable(),

                TextExt::make('Email', 'email')
                    ->saveOnChange()
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

                Foreign::make('Причина расформирования заказа', 'cancel_reason_id')
                    ->options((new Options('cancelReason'))
                        ->isJson()->orderBy('priority', 'asc'))
                    ->filter()
                    ->onlyForm()->className('9'),

                Textarea::make('Другая причина расформирования заказа', 'cancel_reason')->onlyForm()->className('91'),

                Foreign::make('Статус комплектации', 'complect_status_id')
                    ->options((new Options('complectation'))
                        ->isJson())
                    ->filter()
                    ->onlyForm(),
                Foreign::make('Статус оплаты', 'is_online_payed')
                    ->options((new Options('paymentstatus'))
                        ->isJson())
                    ->filter()
                    ->onlyForm(),
                LiqPayOnline::make('LiqPayOnline оплата')->onlyForm(),
                MonoPayParts::make('MonoPayParts оплата')->onlyForm(),
            ],

            'Доставка и оплата' => [

                NpTtn::make('Создать ЕН Новой почты')->onlyForm(),

                Text::make('Трекинг', 'tracking_num')->onlyForm(),

                OrderDelivery::make('Доставка', 'delivery_id')->saveOnChange()
                    ->resetWarehouseFieldsOnChange()
                    ->options((new Options('delivery'))
                        ->isJson())
                    ->filter('foreign')->action(),
                OrderCity::make('Город', 'city_id')->saveOnChange()
                    ->resetWarehouseFieldsOnChange()
                    ->options((new Options('city'))
                        ->isJson())
                    ->filter()->onlyForm()->className('1 2 3 4 5 6 7 8 9'),

                Textarea::make('Адрес', 'address')
                    ->onlyForm()->className('1 2'),

                ForeignAjaxExt::make('Пункт самовывоза', 'delivery_pickup_point_id')->saveOnChange()
                    ->options((new Options('deliveryPickupPoint'))
                        ->isJson())
                    ->filter()->onlyForm()->className('4'),

                ForeignAjaxWarehouses::make('Отделение новой почты', 'np_warehouse_id')->saveOnChange()
                    ->options((new Options('npWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('3'),

                ForeignAjaxWarehouses::make('Отделение укрпочты', 'ukrposhta_warehouse_id')->saveOnChange()
                    ->options((new Options('ukrposhtaWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('6'),

                ForeignAjaxWarehouses::make('Отделение justin', 'justin_warehouse_id')->saveOnChange()
                    ->options((new Options('justinWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('7'),

                ForeignAjaxWarehouses::make('Отделение meest', 'meest_warehouse_id')->saveOnChange()
                    ->options((new Options('meestWarehouse'))
                        ->isJson())
                    ->filter()->onlyForm()->className('8'),

                PayMethods::make('Метод оплаты', 'pay_method_id')
                    ->options((new Options('payMethod'))
                        ->isJson())
                    ->filter('foreign'),
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
                ForeignFOP::make('Юридична особа для замовлення', 'legal_entities_recipient_id')
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
                RequisitesSMS::make('Реквизиты в sms')->onlyForm(),
                OrderChangedEmail::make('Обновленный заказ на email')->onlyForm(),
            ],
            'Логи' => [
                LogsForOrders::make('Логи')->onlyForm(),
            ],
        ];
    }

    public function getFilterScope($collection)
    {
        //return $collection->where('is_quick', 0); //без быстрых заказов
        return $collection->whereIn('is_quick', [1, 0]); //быстрые и обычные заказы
        //return $collection->whereIn('is_quick', [1,0])->where(DB::raw('COALESCE(prom_id,0)'), '=', 0); //быстрые и обычные заказы и пром(с корректным null)
    }

    public function getList()
    {
        Session::remove('orderId');
        $list = new ListingOrders($this);
        $listingRecords = $list->body();

        return view('cms.fields.ordertable', compact('list', 'listingRecords'));
    }

    public function cards(): array
    {
        return [
            //ChartOrders::class
        ];
    }

    public function buttons(): array
    {
        return [
            Export::class,
        ];
    }

    //Добавить действие для откр. в новом окне
    public function actions()
    {
        return ActionsOrders::make()->orderopen()->insert()->update()->revisions()->delete();
    }

    // подмена поля contact (контактные данные на фио)
    public function getFilter()
    {
        $filter = session($this->getSessionKeyFilter());

        //Фильтр по номеру заказа. Если только цифровое поле, ищем по ID. Иначе по NUM
        if ($filter && isset($filter['filter']['num']) && is_numeric($filter['filter']['num'])) {
            $filter['filter']['id'] = $filter['filter']['num'];
            unset($filter['filter']['num']);
        }

        if ($filter && isset($filter['filter']['contact'])) {
            $filter['filter']['first_name'] = $filter['filter']['contact'];
            $filter['filter']['last_name'] = $filter['filter']['contact'];
            $filter['filter']['phone'] = $filter['filter']['contact'];
            $filter['filter']['email'] = $filter['filter']['contact'];
            //$filter['filter']['address'] = $filter['filter']['contact'];
            $filter['filter']['comment'] = $filter['filter']['contact'];
            $filter['filter']['products.product'] = $filter['filter']['contact'];
            unset($filter['filter']['contact']);
        }

        return $filter;
    }

    //переопределение поиска по полям - для статуса и фио сделать свою логику
    //Доработано с учетом поиска по всем полям контактов
    public function getCollection($getAllRecords = false)
    {
        $contactFields = ['first_name', 'last_name', 'phone', 'email', 'comment']; //Список контактных полей таблицы заказа, по которым ищем
        $relationContactFields = ['products.product']; // Список связей, по которым ищем среди Контактов
        $collection = $this->model()->with($this->relations);
        $filter = $this->getFilter();
        $orderBy = $this->getOrderBy();
        $perPage = $this->getPerPageThis();
        $collection = $this->getFilterScope($collection);

        if (isset($filter['filter']) && is_array($filter['filter'])) {
            $allFields = $this->getAllFields();

            $contactConditions = [];
            $otherConditions = [];
            $relationConditions = []; // Для условий, связанных с отношениями HasOne
            $relationContactsHasManyConditions = [];

            foreach ($filter['filter'] as $field => $value) {
                if (is_null($value) || $value === '') {
                    continue;
                }

                $isTextField = $this->isTextField($allFields, $field);
                $fieldName = $this->getFieldName($allFields, $field);

                if ($hasOneRelation = $this->getRelationsHasOne($allFields, $field)) {
                    // Поле, которое является отношением HasOne
                    $relationConditions[] = function ($query) use ($hasOneRelation, $value, $isTextField, $fieldName) {
                        $query->whereHas($hasOneRelation, function ($subquery) use ($fieldName, $value, $isTextField) {
                            $subquery->where($fieldName, '=', $value);
                            if ($isTextField) {
                                $subquery->orWhere($fieldName, 'LIKE', '%'.trim($value).'%');
                            }
                        });
                    };
                }  elseif (in_array($field, $relationContactFields)) {
                    // Поле, которое является отношением не HasOne. На данный момент ищем по составу заказа - название и артикул. Потом доработать для универсальности
                    $relationContactsHasManyConditions[] = function ($query) use ($field, $value) {
                        $query->whereHas($field, function ($subquery) use ($value) {
                            $subquery->where("title->".\App::getLocale(), "LIKE", "%".trim($value)."%")
                                ->orWhere('code', "LIKE", "%".trim($value)."%");
                        });
                    };
                    //
                } else {
                    if (is_array($value)) {
                        if ($value['from'] || $value['to']) {
                            if ($value['from']) {
                                $collection = $collection->where($field, '>=', $value['from']);
                            }

                            if ($value['to']) {
                                $collection = $collection->where($field, '<=', $value['to'].' 23:59:59');
                            }
                        }

                        continue;
                    }
                    if (in_array($field, $contactFields)) {
                        // Контактное поле, добавляем условие в группу контактных условий
                        $contactConditions[] = function ($query) use ($field, $value, $contactFields) {
                            //$query->orWhereRaw('LOWER(`' . $field . '`) LIKE ?', ['%' . trim(mb_strtolower($value)) . '%']);

                            //--
                            $keywords = explode(' ', mb_strtolower(trim($value)));
                            foreach ($keywords as $keyword) {
                                $query->where(function ($subquery) use ($field, $contactFields, $keyword) {
                                    foreach ($contactFields as $field) {
                                        $subquery->orWhere($field, 'LIKE',  '%' . $keyword . '%');
                                    }
                                });
                            }
                            //--
                        };
                    } else {
                        // Не контактное поле, добавляем условие в группу остальных условий
                        $otherConditions[] = function ($query) use ($field, $value, $allFields, $isTextField) {
                            $query->where($field, '=', $value);
                            if($isTextField) {
                                $query->orWhere($field, 'LIKE',  '%' . $value . '%');
                            }
                        };
                    }
                }
            }

            // Строим запрос для контактных полей (ИЛИ)
            if (!empty($contactConditions)) {
                $collection = $collection->where(function ($query) use ($contactConditions, $relationContactsHasManyConditions) {
                    foreach ($contactConditions as $condition) {
                        $query->orWhere($condition);
                    }
                    if (!empty($relationContactsHasManyConditions)) {
                        foreach($relationContactsHasManyConditions as $relationCondition) {
                            $query->orWhere($relationCondition);
                        }
                    }
                });
            }

            // Строим запрос для остальных полей (И)
            if (!empty($otherConditions)) {
                foreach ($otherConditions as $condition) {
                    $collection = $collection->where($condition);
                }
            }

            // Добавляем условия для полей, являющихся отношениями
            if (!empty($relationConditions)) {
                $collection = $collection->where(function ($query) use ($relationConditions) {
                    foreach ($relationConditions as $condition) {
                        $query->orWhere($condition);
                    }
                });
            }
        }

        if ($getAllRecords) {
            return $collection->orderByRaw($orderBy)->get();
        }

        return $collection->orderByRaw($orderBy)->paginate($perPage);
    }

}
