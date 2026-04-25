<?php

namespace App\Cms\Tree\Templates;

use App\Cms\Definitions\BlocksSeocatalog;
use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\ResourceTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Definition;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class SeocatalogSection extends ResourceTree
{
    protected $titleDefinition = 'SEO-категория (посадочная страница)';

    public $action = 'SeocatalogController@category';

    public function fields()
    {
        return [
            'Общее' => [
                Id::make('#', 'id')->sortable(),
                Text::make('Заголовок', 'title')->language()->comment('Проверьте корректность перевода на все языки'),
                //Froala::make('Описание', 'description')->language(),
                Text::make('Slug', 'slug'),

                Text::make('Url', 'url')
                    ->language()
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('tb_tree')->ignore(request('id')),
                    ])->onlyForm()->onlyForm()->comment('Мультиязычный url. Имеет бОльший приоритет по сравнению с полем slug. Если для какого-то языка он заполнен, то в данном языке раздел доступен только по этому url, а поле slug игнорируется.'),
                Image::make('Картинка', 'picture'),
                Checkbox::make('Активно', 'is_active'),

                /*Select::make('Тип', 'menu_type')
                    ->options([
                        '' => 'Выбрать тип',
                        'App\\Models\\Tree' => 'Статья дерева',
                        'App\\Models\\Category' => 'Категория товара',
                        'App\\Models\\MenuSection' => 'Секция с заголовком и подкатегориями', //Заглушка
                    ]),*/
                Hidden::make('Тип', 'menu_type')->hasOne('treemenu')->default('App\\Models\\MenuSection'),

                /*ForeignTree::make('Статья', 'menu_id')->hasOne('treemenu')
                    ->options((new Options('menu'))
                        ->keyField('title')),
                Text::make('Ссылка (без домена, напр., /catalog/...)', 'url')->hasOne('treemenu')->language(),
                Checkbox::make('Открывать в новой вкладке', 'is_target_blank')->hasOne('treemenu'),*/

            ],
            'Блоки' => [
                Definition::make('Блоки')
                    ->morphMany('blocks', BlocksSeocatalog::class)
            ],
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
