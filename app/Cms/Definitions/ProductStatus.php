<?php

namespace App\Cms\Definitions;

use App\Models\ProductStatus as ProductStatusModel;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Color;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;

class ProductStatus extends Resource
{
    public $model = ProductStatusModel::class;

    public $title = 'Статусы товаров';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'Основное' => [
            Id::make('#', 'id')->sortable(),
            Text::make('Заголовок', 'title')->filter()->sortable()->language(),
            Text::make('Описание', 'description')->language(),
            Checkbox::make('Отображать', 'is_active')->filter()->sortable(),
            Color::make('Цвет', 'color'),
            Checkbox::make('Показывать кнопку сообщить о наличии', 'report_availability')->filter()->sortable(),
                ],
            'Настройки для Feeds' => [
            Select::make('Hotline stock (доступность товара)', 'hotline_stock')
                ->options([
                    'В наявності' => 'В наличии',
                    'Під замовлення' => 'Под заказ',
                    'Немає' => 'Нет в наличии',
                ])->default(0)->onlyForm()->className('col-md-6')->comment('Укажите, как данный статус отображается в фиде Hotline в поле "Доступность товара", тэг "stock"'),
            Select::make('Hotline shipping (отгрузка со склада)', 'hotline_shipping')
                ->options([
                    '0' => 'Сегодня',
                    '1' => '1 день',
                    '2' => '2 дня',
                    '3' => '3 дня',
                    '4' => '4 дня',
                    '5' => '5 дней',
                    '6' => '6 дней',
                    '3,4,5' => '3-5 дней',
                    '5,6,7' => '5-7 дней',
                    '7,8,9,10' => '7-10 дней',
                    '10,11,12,13,14' => '10-14 дней',
                    'none' => 'Уточнить у продавца',
                ])->default(0)->onlyForm()->className('col-md-6')->comment('Укажите, как данный статус отображается в фиде Hotline в поле "Отгрузка со склада", тэг "shipping". Если указывается диапазон дней, то значимым считается максимальное количество дней.'),

                Select::make('Google availability (наличие)', 'google_availability')
                    ->options([
                        'in_stock' => 'В наличии',
                        'backorder' => 'Под заказ',
                        'preorder' => 'Предзаказ',
                        'out_of_stock' => 'Нет в наличии',
                    ])->default('out_of_stock')->onlyForm()->className('col-md-6')->comment('Укажите, как данный статус отображается в фиде Google в поле "Наличие", тэг "availability"'),

                Select::make('Schema.org availability (наличие)', 'schema_availability')
                    ->options([
                        'BackOrder' => 'BackOrder',
                        'Discontinued' => 'Discontinued',
                        'InStock' => 'InStock',
                        'InStoreOnly' => 'InStoreOnly',
                        'LimitedAvailability' => 'LimitedAvailability',
                        'MadeToOrder' => 'MadeToOrder',
                        'OnlineOnly' => 'OnlineOnly',
                        'OutOfStock' => 'OutOfStock',
                        'PreOrder' => 'PreOrder',
                        'PreSale' => 'PreSale',
                        'Reserved' => 'Reserved',
                        'SoldOut' => 'SoldOut',
                    ])->default('OutOfStock')->onlyForm()->className('col-md-6')->comment('Укажите, как данный статус отображается в разметке Schema.org в поле "Наличие", тэг "availability"'),


            ]
        ];
    }
}
