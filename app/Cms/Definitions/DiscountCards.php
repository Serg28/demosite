<?php

namespace App\Cms\Definitions;

use App\Models\DiscountCard;
use Illuminate\Support\Str;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Select;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Fields\Textarea;
use Linecore\Cms\Services\Actions;

class DiscountCards extends Resource
{
    public $model = DiscountCard::class;

    public $title = 'Дисконтні картки';

    protected $orderBy = 'id desc';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Номер картки', 'code')->filter()->default($this->generateCode()),
            Text::make('Штрих-код', 'barcode')->onlyForm(),
            Text::make("Ім'я власника", 'name')->filter(),
            Text::make('Телефон', 'phone')->filter(),
            Text::make('Email', 'email')->onlyForm(),
            Text::make('Адреса', 'address')->onlyForm(),
            Text::make('Місто', 'city')->onlyForm(),
            Textarea::make('Коментар', 'comment')->onlyForm(),
            Select::make('Тип знижки', 'type')->options([
                'percent' => 'Відсоток (%)',
                'fixed'   => 'Фіксована сума (грн)',
            ])->filter(),
            Number::make('Значення знижки', 'value')->filter(),
            Checkbox::make('Використовується з розстрочкою', 'use_for_installments')->onlyForm(),
            Checkbox::make('Використовується з промокодами', 'use_for_promotional')->onlyForm(),
            Checkbox::make('Активно', 'is_active')->filter()->fastEdit(),
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
