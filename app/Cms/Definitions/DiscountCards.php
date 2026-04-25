<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignAjaxUser;
use App\Models\DiscountCard;
use Illuminate\Support\Str;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Export;
use Vis\Builder\Services\Import;

class DiscountCards extends Resource
{
    public $model = DiscountCard::class;

    public $title = 'Дисконтные карты';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Номер карты', 'code')->filter(),
            Text::make('Штрих-код', 'barcode')->filter(),
            Text::make('ФИО', 'name')->filter()->sortable(),
            Text::make('Телефон', 'phone')->filter()->sortable(),
            Text::make('Email', 'email')->filter(),
            Text::make('Адрес', 'address')->onlyForm(),
            Text::make('Город', 'city')->onlyForm(),
            Text::make('Комментарий', 'comment')->onlyForm(),
            /*ForeignAjaxUser::make('Клиент', 'user_id')
                ->onlyForm()
                ->options((new Options('user'))
                    ->keyField('phone')
                    ->keyField('first_name')
                    ->keyField('last_name')
                    ->keyField('email'))
                ->filter(),*/
            Number::make('Скидка, %', 'discount')->filter(),
            Checkbox::make('Активно', 'is_active'),
        ];
    }

    public function generateCod()
    {
        return Str::random(random_int(10, 20));
    }

    public function buttons(): array
    {
        return [
            //Export::class,
            //Import::class,
        ];
    }
}
