<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignTree;
use App\Models\MenuFooter as FooterMenuModel;
use Vis\Builder\Definitions\ResourceAdditionTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class MenuFooter extends ResourceAdditionTree
{
    public $model = FooterMenuModel::class;

    public $title = 'Меню Футер';

    protected $cacheTag = 'menu_footer';

    public function fields()
    {
        return [
            Hidden::make('#', 'id'),
            Text::make('Заголовок', 'title')->language(),
            Image::make('Изображение/иконка', 'picture')->filter()->sortable(),
            Select::make('Тип', 'menu_type')
                ->options([
                    '' => 'Выбрать тип',
                    //'App\\Models\\MenuAnchor' => 'Якорь', //Заглушка
                    'App\\Models\\News' => 'Новость или статья',
                    'App\\Models\\Tree' => 'Документ из структуры сайта',
                    'App\\Models\\Category' => 'Категория товара',
                ]),

            ForeignTree::make('Статья', 'menu_id')
                ->options((new Options('menu'))
                    ->keyField('title')),
            Text::make('Ссылка', 'url')->language(),
            Checkbox::make('Открывать в новой вкладке', 'is_target_blank'),
            Checkbox::make('Отображать', 'is_active'),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }
}
