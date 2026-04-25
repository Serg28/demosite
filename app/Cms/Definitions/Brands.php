<?php

namespace App\Cms\Definitions;

use App\Models\Brand;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Text;

class Brands extends Resource
{
    public $model = Brand::class;

    public $title = 'Бренды';

    protected $orderBy = 'priority asc';

    protected $isSortable = true;

    public function fields()
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Название', 'title')->language()->filter()->sortable()->transliteration('slug', true),
                Text::make('Url', 'slug')->filter()->rules([
                    'required',
                    Rule::unique('news')->ignore(request('id')),
                ]),
                Froala::make('Описание', 'description')->language()->onlyForm(),
                Image::make('Лого', 'picture')->filter()->sortable(),
                Number::make('Порядок сортировки', 'priority')->filter()->sortable(),
                Checkbox::make('Показать', 'is_active')->filter()->sortable(),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
