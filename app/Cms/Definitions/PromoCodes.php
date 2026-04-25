<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\PromocodeProductsCodes;
use App\Models\PromoCode;
use Illuminate\Support\Str;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Date;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;

class PromoCodes extends Resource
{
    public $model = PromoCode::class;

    public $title = 'Промокода';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Код', 'code')->default($this->generateCod())->filter(),
            Number::make('Скидка, %', 'sale')->filter(),
            Select::make('Тип', 'type')->options([
                'once' => 'Одноразовый',
                'reusable' => 'Многоразовый',
            ]),
            Checkbox::make('Использован', 'is_used'),
            Checkbox::make('Активно', 'is_active'),
            Date::make('Дата окончания', 'date_exp'),
            Checkbox::make('Применять к товарам с рассрочкой/оплатой частями', 'use_for_installments')->onlyForm(),
            Checkbox::make('Применять к акционным товарам (старая цена больше текущей)', 'use_for_promotional')->onlyForm(),
            Checkbox::make('Применять с дисконтными картами', 'use_for_discount_cards')->onlyForm(),
            PromocodeProductsCodes::make('Артикулы')
                ->options((new Options('applicableProducts'))->isJson())
                ->onlyForm(false),
        ];
    }

    public function generateCod()
    {
        return Str::random(random_int(10, 20));
    }
}
