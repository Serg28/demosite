<?php

namespace App\Cms\Tree\Templates;

use App\Models\MorphOne\Seo;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\ResourceTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Text;

class Seocatalog extends ResourceTree
{
    protected $titleDefinition = 'SEO-категория (ссылка на произвольный URL)';

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
                ])->onlyForm()->comment('Необязательное поле. Если ниже не указана ссылка, то страница будет доступна по указанному здесь значению slug и будет работать как посадочная страница'),

                Text::make('Ссылка (без домена, напр., /catalog/...)', 'url')->hasOne('treemenu')->language()->comment('Используйте это поле для указания произвольной ссылки на любой раздел сайта. Например, путь к странице каталога с результатами фильтрации. Для каждого языка укажите свою ссылку'),

                /*Text::make('Ссылка (без домена, напр., /catalog/...', 'url')
                    ->language()
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('tb_tree')->ignore(request('id')),
                    ])->onlyForm()->onlyForm()->comment('Мультиязычный url. Имеет бОльший приоритет по сравнению с полем slug. Если для какого-то языка он заполнен, то в данном языке раздел доступен только по этому url, а поле slug игнорируется.'),*/
                Image::make('Картинка', 'picture'),
                Checkbox::make('Активно', 'is_active'),
            ],
            /*'Блоки' => [
                Definition::make('Блоки')
                    ->morphMany('blocks', BlocksSeocatalog::class)
            ],*/
            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }
}
