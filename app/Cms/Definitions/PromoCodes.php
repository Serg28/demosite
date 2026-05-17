<?php

namespace App\Cms\Definitions;

use App\Models\PromoCode;
use Illuminate\Support\Str;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Date;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\ManyToMany;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Relations\Options;
use Linecore\Cms\Fields\Select;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Services\Actions;

class PromoCodes extends Resource
{
    public $model = PromoCode::class;

    public $title = 'Промокоди';

    protected $orderBy = 'id desc';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Код', 'code')->filter()->default($this->generateCode()),
            Select::make('Тип', 'type')->options([
                'percent' => 'Відсоток (%)',
                'fixed'   => 'Фіксована сума (грн)',
            ])->filter(),
            Number::make('Значення', 'value')->filter()->comment('Відсоток або фіксована сума залежно від типу'),
            Number::make('Мінімальна сума замовлення, грн', 'min_order_amount')->onlyForm(),
            Date::make('Дата закінчення', 'expires_at')->onlyForm(),
            Select::make('Тип використання', 'usage_type')->options([
                'reusable' => 'Багаторазовий',
                'once'     => 'Одноразовий',
            ])->filter(),
            Checkbox::make('Використано', 'is_used')->onlyForm(),
            Number::make('Максимум використань', 'max_uses')->onlyForm()->comment('Залиште порожнім для необмеженого'),
            Number::make('Використано разів', 'used_count')->readonlyForEdit(),
            Checkbox::make('Використовується з розстрочкою', 'use_for_installments')->onlyForm(),
            Checkbox::make('Промо-тип (маркетинговий)', 'use_for_promotional')->onlyForm(),
            Checkbox::make('Використовується з дисконтними картками', 'use_for_discount_cards')->onlyForm(),
            Checkbox::make('Активно', 'is_active')->filter()->fastEdit(),
            ManyToMany::make('Обмеження по товарах')
                ->options(
                    (new Options('products'))
                        ->keyField('title')
                        ->isJson()
                        ->orderBy('id', 'desc')
                )
                ->className('col-md-12')
                ->onlyForm(),
        ];
    }

    public function generateCode(): string
    {
        return Str::random(random_int(10, 20));
    }

    public function actions(): Actions
    {
        return Actions::make($this)->insert()->update()->delete();
    }
}
