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
            Select::make('Тип знижки', 'type')->options([
                'percent' => 'Відсоток (%)',
                'fixed'   => 'Фіксована сума (грн)',
            ])->filter(),
            Number::make('Значення знижки', 'value')->filter(),
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
