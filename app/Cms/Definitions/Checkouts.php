<?php

namespace App\Cms\Definitions;

use App\Models\Checkout;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Checkouts extends Resource
{
    public $model = Checkout::class;

    public $title = 'Платежные системы';

    protected $isSortable = true;

    protected $orderBy = 'priority asc';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            Text::make('Имя', 'title')->filter()->sortable()->language()->transliteration('slug', true),
            Text::make('Slug', 'slug')->filter()->sortable()->rules([
                'required',
                Rule::unique('checkouts')->ignore(request('id')),
            ]),
            /*Textarea::make('Настройки (в формате .ini)', 'settings')->onlyForm(),*/
            /*  Image::make('Изображение', 'picture')->onlyForm(),*/
            Checkbox::make('Показывать', 'is_active')->filter()->sortable()->fastEdit(),
            Checkbox::make('Активировать функционал предоплаты', 'is_active_prepayment')->default(0)->onlyForm()->comment('Активируйте, если метод оплаты подразумевает Предоплату. Соответствующие поля появятся при редактировании метода оплаты'),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update();
    }
}
