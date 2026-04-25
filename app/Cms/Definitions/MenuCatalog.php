<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignTree;
use App\Cms\Fields\TextExt;
use App\Models\MenuCatalog as MenuCatalogModel;
use Vis\Builder\Definitions\ResourceAdditionTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class MenuCatalog extends ResourceAdditionTree
{
    public $model = MenuCatalogModel::class;

    public $title = 'Меню каталога';

    protected $cacheTag = 'menu_catalog';

    public function fields()
    {
        return [
            Hidden::make('#', 'id'),
            Text::make('Заголовок', 'title')->language()->comment('Проверьте корректность заголовка во всех языковых версиях. Особенно, если название латиницей'),
            Image::make('Изображение/иконка', 'picture')->filter()->sortable(),
            Text::make('Класс css', 'css_class')->onlyForm()->comment('Класс CSS, который применяется к пункту меню. Напр., sale - оранжевый текст'),
            Select::make('Тип', 'menu_type')
                ->options([
                    '' => 'Ссылка',
                    'App\\Models\\Tree' => 'Статья дерева',
                    'App\\Models\\Category' => 'Категория товара',
                    //'App\\Models\\MenuColSection' => 'Колонка с секциями подкатегорий (объединяет несколько секций)', //Заглушка
                    //'App\\Models\\MenuSection' => 'Секция с заголовком и подкатегориями', //Заглушка
                ])->onlyForm(),

            ForeignTree::make('Статья', 'menu_id')
                ->options((new Options('menu'))
                    ->keyField('title'))->onlyForm(),
            TextExt::make('Ссылка (без домена, напр., /catalog/...)', 'url')->language()->autoTranslate('false')->onlyForm()->comment('Внимание! Если ссылка на текущий сайт, то домен указывать не нужно, напр., /catalog/... Проверьте, чтобы ссылки были корректными для всех языков. '),
            Checkbox::make('Открывать в новой вкладке', 'is_target_blank')->onlyForm()->className('col-md-6'),
            Checkbox::make('Отображать', 'is_active')->className('col-md-6'),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }
}
