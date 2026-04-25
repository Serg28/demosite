<?php

namespace App\Cms\Tree\Templates;

use App\Cms\Fields\ForeignSeoCatTree;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\ResourceTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;

class SeocatalogLink extends ResourceTree
{
    protected $titleDefinition = 'SEO-категория (ссылка на раздел сайта)';

    public $action = 'SeocatalogController@category';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language()->comment('Проверьте корректность перевода на все языки'),
                //Froala::make('Описание', 'description')->language(),
                Text::make('Slug', 'slug')->rules([
                    Rule::unique('tb_tree')->ignore(request('id')),
                ])->onlyForm()->comment('Необязательное поле. Если ниже не выбран Тип ссылки, то страница будет доступна по указанному здесь значению slug и будет работать как посадочная страница'),

                /*Text::make('Url', 'url')
                    ->language()
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('tb_tree')->ignore(request('id')),
                    ])->onlyForm()->onlyForm()->comment('Мультиязычный url. Имеет бОльший приоритет по сравнению с полем slug. Если для какого-то языка он заполнен, то в данном языке раздел доступен только по этому url, а поле slug игнорируется.'),
                Image::make('Картинка', 'picture'),
                Checkbox::make('Активно', 'is_active'),*/

                Select::make('Тип ссылки', 'menu_type')->hasOne('treemenu')
                    ->options([
                        '' => 'Выбрать тип',
                        'App\\Models\\Tree' => 'Документ из структуры сайта',
                        'App\\Models\\Category' => 'Категорию товара',
                        //'App\\Models\\MenuSection' => 'Секция с заголовком и подкатегориями', //Заглушка
                    ]),

                ForeignSeoCatTree::make('Статья', 'menu_id')->hasOne('treemenu')
                    ->options((new Options('menu'))
                        ->keyField('title')),
                //Text::make('Ссылка (без домена, напр., /catalog/...)', 'url')->hasOne('treemenu')->language(),

                Image::make('Картинка', 'picture'),

                Checkbox::make('Открывать в новой вкладке', 'is_target_blank')->hasOne('treemenu'),

            ],
            /*'Блоки' => [
                Definition::make('Блоки')
                    ->morphMany('blocks', Blocks::class)
            ],*/
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
