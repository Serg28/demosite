<?php

namespace App\Cms\Definitions;

use App\Models\MorphOne\Seo;
use App\Models\SeoGroups as SeoGroupsModel;
use Litvin\Redirectmap\Service\Import;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Export;

class SeoGroups extends Resource
{
    public $model = SeoGroupsModel::class;

    public $title = 'SEO-теги для страниц';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                //Text::make('Название', 'title')->language()->filter(),
                Select::make('Тип страницы', 'view')
                    ->options([
                        'product.index' => 'Карточка товара',
                        'default_seo_tags.empty' => 'По-умолчанию (если другие не указаны)'
                    ]),
                Checkbox::make('Отображать', 'is_active')->fastEdit(),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->delete();
    }

    /*public function buttons()
    {
        return [
            Import::class,
            Export::class
        ];
    }*/
}
