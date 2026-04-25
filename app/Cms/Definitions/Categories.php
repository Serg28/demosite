<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\CategoryCharacteristic;
use App\Cms\Fields\ForeignAjaxExt;
use App\Cms\Fields\ForeignTreeCategory;
use App\Cms\Fields\TextExt;
use App\Models\Category;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\ResourceAdditionTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToManyMultiSelect;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class Categories extends ResourceAdditionTree
{
    public $model = Category::class;

    public $title = 'Категории';

    protected $cacheTag = 'сategories';

    public function fields()
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),
                Image::make('Фото', 'picture')->onlyForm(),
                Text::make('Заголовок', 'title')->language()->filter()->sortable()
                    ->transliteration('slug', true),
                Text::make('Slug', 'slug')->filter()->rules([
                    'required',
                    Rule::unique('categories')->ignore(request('id')),
                ]),

                TextExt::make('Url', 'url')->language()->filter()->rules([
                    'required',
                    Rule::unique('categories')->ignore(request('id')),
                ])->autoTranslate(false)->onlyForm(),
/*
                ForeignTreeCategory::make('Родительская категория', 'parent_id')
                    ->options((new Options('children'))->isJson())
                    ->onlyForm(),
                */
                //Froala::make('Гарантия (блок у товара)', 'guarantee')->language()->onlyForm(),
                Image::make('Иконка в меню', 'ico')->onlyForm(),
                Checkbox::make('Отображать', 'is_active')->fastEdit(\Sentinel::inRole('admin')),
            ],
            'Характеристики' => [
                //Это стандарнтый список с перетаскиванием
                /*ManyToManyMultiSelect::make('Характеристики')
                    ->options(
                        (new Options('characteristics'))
                    ),*/

                //Это список в виде таблицы
                CategoryCharacteristic::make('Фильтры категории')
                    ->hasMany('category_characteristics', CategoryCharacteristics::class),

            ],
            'Связи товаров' => [
                ForeignAjax::make('Характеристика для связи', 'characteristic_id')
                    ->options((new Options('characteristicRelative'))
                        ->isJson())
                    ->onlyForm(),

                ManyToManyMultiSelect::make('Характеристики для вывода на товаре')
                    ->options(
                        (new Options('characteristicsForProductRelative'))
                    ),

            ],
            'Внешний вид' => [
                Select::make('Тип товаров (для шаблона отображения)', 'product_type')
                    ->options([
                        '' => 'Не указан - берем у родительской категории',
                        'actions' => 'Раздел акции'
                    ])->onlyForm()->comment('Укажите, какой тип товаров содержит категория или подкатегории. Используется для определения шаблона отображения в каталоге и карточке товара. Если пусто - то наследуется от родительской категории или выше по дереву. Если не указать совсем - используется универсальный шаблон'),
            ],
            'Настройки для Feeds' => [

                /*Text::make('Google Category ID', 'google_id')->onlyForm(),*/

                ForeignAjaxExt::make('Категория Google', 'google_id')
                    ->options((new Options('googleCategory'))
                        ->isJson())->setMinimumSearchLength(0)->onlyForm()->comment('Выберите категорию из рубрикатора Google. Это применимо только к конечным категориям, которые содержат товары. Все товары, относящиеся к выбранной категории, будут экспортироваться в Google Feed с указанной Google-категорией в поле "google_product_category"'),

                ForeignAjaxExt::make('Категория Hotline', 'hotline_id')
                    ->options((new Options('hotlineCategory'))
                        ->isJson())->setMinimumSearchLength(0)->onlyForm()->comment('Выберите категорию из рубрикатора Hotline. Это применимо только к конечным категориям, которые содержат товары. Все товары, относящиеся к выбранной категории, будут экспортироваться в Hotline Feed с указанной здесь рубрикой'),
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->clone()->revisions()->delete();
    }

    public function getListing()
    {
        $current = $this->model()->findOrFail(request('node', 1));
        $children = $current->children();
        $children = $children->withCount('children')->defaultOrder()->paginate($this->getPerPageThis());
        $head = $this->head();
        $definition = $this;

        $children->map(function ($item, $key) use ($head, $definition) {
            $item->fields = clone $head;
            $item->fields->map(function ($item2, $key) use ($item, $definition) {
                $item->fields[$key] = clone $item2;
                $item2->setValue($item);

                $item->fields[$key]->value = $item2->getValueForList($definition);
            });
        });

        return $children;
    }
}
