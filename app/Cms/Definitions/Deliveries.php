<?php

namespace App\Cms\Definitions;

use App\Models\Delivery;
use Linecore\Cms\Definitions\Resource;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\ManyToMany;
use Linecore\Cms\Fields\Number;
use Linecore\Cms\Fields\Relations\Options;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Services\Actions;

class Deliveries extends Resource
{
    public $model = Delivery::class;

    public $title = 'Способи доставки';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields(): array
    {
        return [
            'Основне' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Назва', 'title')->filter()->sortable()->language(),
                Text::make('Slug', 'slug')->onlyForm(),
                Number::make('Вартість', 'price')->default(0),
                Number::make('Безкоштовно від', 'free_cost')->onlyForm(),
                Number::make('Пріоритет', 'priority')->default(10)->onlyForm(),
                Checkbox::make('Активний', 'is_active')->filter()->sortable(),
            ],
            'Міста' => [
                Checkbox::make('Доступний для всіх міст', 'is_for_all_cities')
                    ->default(true)
                    ->onlyForm()
                    ->comment('Якщо увімкнено — доставка доступна в усіх містах. Якщо вимкнено — лише в обраних містах нижче.'),
                ManyToMany::make('Міста')
                    ->options(new Options('cities'))
                    ->className('col-md-12')
                    ->onlyForm(),
            ],
            'Оплата' => [
                ManyToMany::make('Способи оплати')
                    ->options(new Options('pay_methods'))
                    ->className('col-md-12'),
            ],
        ];
    }

    public function actions(): Actions
    {
        return Actions::make($this)->insert()->update()->delete();
    }
}
