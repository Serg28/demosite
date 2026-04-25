<?php

namespace App\Cms\Definitions;

use App\Models\PayMethod;
use Vis\Builder\Fields\Number;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Foreign;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class PayMethods extends Resource
{
    public $model = PayMethod::class;

    public $title = 'Способы оплаты';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'Общая' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Название', 'title')->filter()->sortable()->language(),
                Froala::make('Краткое описание для чекаута', 'short_description')->language()->onlyForm(),
                Image::make('Логотип', 'picture'),
                Foreign::make('Система оплаты', 'checkout_id')
                    ->options(
                        (new Options('checkout'))->isJson()
                    )
                    ->default('Выберите систему'),

                Checkbox::make('Показывать', 'is_active')->filter()->sortable(),

            ],
            'Настройки' => [
                Checkbox::make('Подразумевает оплату частями или кредит', 'is_payparts')->filter()->sortable()->onlyForm(),

                Text::make('Комиссия, %', 'commission_rate')->onlyForm()->comment('Размер комиссии от суммы заказа, которая будет добавлена к итоговой сумме заказа'),

                Select::make('Тип платежа', 'type')
                    ->options([
                        'CASHLESS' => 'Безналичный (онлайн, карта, перевод)',
                        'CASH' => 'Наличные',
                    ])->onlyForm()->comment('Этот тип будет указан в чеке')->className('col-md-12'),

                Number::make('Минимальная сумма заказа',
                    'min_order_amount')->default(0)->onlyForm()->comment('Сумма заказа, от которой метод оплаты будет доступен')->className('col-md-12'),
                ManyToMany::make('Отображать для способов доставки')
                    ->options(
                        (new Options('deliveries'))
                    )->className('col-md-12'),

                Checkbox::make('Активировать функционал предоплаты',
                    'is_active_prepayment')->default(0)->onlyForm()->className('col-md-12')->comment('Если отключено, то следующие поля игнорируются, сумма на оплату = 100%. Если активировано, обязательно заполните поля ниже. Иначе сумма предоплаты будет 0, счет на оплату не будет выставлен и после оформления заказа клиент будет отправлен на страницу Спасибо за заказ.')->hide($this->prepaymentIsActive()),
                Number::make('Процент предоплаты',
                    'prepayment_percent')->default(0)->onlyForm()->comment('Процент предоплаты от общей суммы заказа. Только эту сумму клиент будет оплачивать при оформлении заказа')->className('col-md-6')->hide($this->prepaymentIsActive()),
                Number::make('Но не менее, грн',
                    'min_prepayment_amount')->default(0)->onlyForm()->comment('Минимальная сумма предоплаты. Если сумма предоплаты по процентам меньше, то задействуется эта сумма')->className('col-md-6')->hide($this->prepaymentIsActive()),
            ]
        ];
    }

    public function prepaymentIsActive()
    {
        if (request('id')) {
            return !empty($this->model()->find(request('id'))->checkout->is_active_prepayment) ? false : true;
        }
        return false;
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }
}
