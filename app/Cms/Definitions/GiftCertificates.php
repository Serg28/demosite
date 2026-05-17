<?php

namespace App\Cms\Definitions;

use App\Models\GiftCertificate;
use Illuminate\Support\Str;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Date;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Services\Actions;

class GiftCertificates extends Resource
{
    public $model = GiftCertificate::class;

    public $title = 'Подарункові сертифікати';

    protected $orderBy = 'id desc';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Код', 'code')->filter()->default($this->generateCode()),
            Number::make('Номінал, грн', 'amount')->filter(),
            Checkbox::make('Активний', 'is_active')->filter()->fastEdit(),
            Checkbox::make('Використаний', 'is_used')->filter()->fastEdit(),
            Date::make('Дата закінчення', 'expires_at')->onlyForm(),
            Checkbox::make('З дисконтними картками', 'use_for_discount_cards')->onlyForm()->default(1),
            Checkbox::make('З акційними товарами', 'use_for_promotional')->onlyForm()->default(1),
            Checkbox::make('З розстрочкою', 'use_for_installments')->onlyForm()->default(0),
        ];
    }

    public function generateCode(): string
    {
        return 'CERT' . implode('', array_map(fn () => random_int(0, 9), range(1, 10)));
    }

    public function actions(): Actions
    {
        return Actions::make($this)->insert()->update()->delete();
    }
}
