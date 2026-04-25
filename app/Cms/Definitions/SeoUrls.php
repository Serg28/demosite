<?php

namespace App\Cms\Definitions;

use App\Models\MorphOne\Seo;
use App\Models\SeoUrls as SeoUrlsModel;
use Litvin\Redirectmap\Service\Import;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Export;

class SeoUrls extends Resource
{
    public $model = SeoUrlsModel::class;

    public $title = 'SEO-теги для URL';

    protected $orderBy = 'id desc';

    public function fields()
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Ссылка', 'link')->language()->filter(),
                Checkbox::make('Отображать', 'is_active')->filter()->fastEdit(),
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
