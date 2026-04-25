<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignTree;
use App\Cms\Fields\ParentNodeMenuHeader;
use App\Models\MenuHeader as MenuHeaderModel;
use Illuminate\Support\Facades\Session;
use Vis\Builder\Definitions\ResourceAdditionTree;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;
use Vis\Builder\Services\Listing;

class MenuHeader extends ResourceAdditionTree
{
    public $model = MenuHeaderModel::class;

    public $title = 'Меню Хеадер';

    protected $cacheTag = 'menu_header';

    public function fields()
    {
        return [
            Hidden::make('#', 'id'),
            ParentNodeMenuHeader::make('parent', 'parent_id')->default(request()->get('node') ?: (Session::get('MenuHeader_node') ?: 1)),
            Text::make('Заголовок', 'title')->language(),
            Image::make('Изображение/иконка', 'picture')->filter()->sortable(),
            Select::make('Тип', 'menu_type')
                ->options([
                    '0' => 'Ссылка',
                    //'App\\Models\\MenuAnchor' => 'Якорь', //Заглушка
                    'App\\Models\\Tree' => 'Документ из структуры сайта',
                    'App\\Models\\Category' => 'Категория товара',
                    //'App\\Models\\MenuColSection' => 'Колонка с секциями подкатегорий (объединяет несколько секций)', //Заглушка
                    //'App\\Models\\MenuSection' => 'Секция с заголовком и подкатегориями', //Заглушка
                ])->action(),

            ForeignTree::make('Статья', 'menu_id')
                ->options((new Options('menu'))
                    ->keyField('title')),
            Text::make('Ссылка (внутренняя - без домена, внешняя - полная)', 'url')->language(),
            Checkbox::make('Открывать в новой вкладке', 'is_target_blank'),
            Checkbox::make('Отображать', 'is_active'),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->delete()->clone();
    }

    public function saveAddForm($request): array
    {
        $result = parent::saveAddForm($request);

        $node = (! empty(request('__node'))) ? request('__node') : ($request['parent_id'] ?: 1);
        $thisRecord = $this->model()->find($result['id']);

        $thisRecord->parent_id = $node;
        $thisRecord->save();

        $root = $this->model()->find($node);
        $thisRecord->prependToNode($root)->save();

        return $result;
    }

    public function getList()
    {
        //Принудительно устанавливаем node в url и сессию. Баг с добавлением новой записи, если нету node
        if (request()->get('node')) {
            Session::put('MenuHeader_node', request()->get('node'));
        } else {
            $node = Session::get('MenuHeader_node') ?: 1;
            header('Location: /admin/menu_header?node='.$node);
        }
        //--

        $list = new Listing($this);
        $listingRecords = $list->body();
        $current = $this->model()->findOrFail(request('node', 1));
        $definition = $this;

        return view('admin::addition_tree.list.table',
            compact('list', 'listingRecords', 'current', 'definition'));
    }
}
