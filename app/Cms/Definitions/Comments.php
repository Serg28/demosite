<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\CommentableModel;
use App\Models\Comment;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;

class Comments extends Resource
{
    public $model = Comment::class;

    public $title = 'Комментарии';

    public function fields()
    {
        return [
            Id::make('#', 'id')->sortable(),
            /*ForeignAjax::make('Товар', 'product_id')
                ->options(
                    (new Options('product'))->isJson()
                )
                ->filter()->sortable(),*/

            Select::make('Тип', 'commentable_type')
                ->options([
                    'product' => 'Отзыв о товаре',
                    'App\\Models\\Tree' => 'Отзыв о компании'
                ])->action(),


            Text::make('Имя', 'name')->filter()->sortable(),
            CommentableModel::make('Документ', 'commentable_type'),
            Textarea::make('Сообщение', 'body')->filter()->sortable(),
            //Textarea::make('Ответ', 'answer')->onlyForm(),
            Number::make('Рейтинг', 'rating')->filter()->sortable(),
            Text::make('parent_id', 'parent_id')->onlyForm()->default(null),
            /*
            ForeignAjax::make('Пользователь', 'user_id')
                ->options(
                    (new Options('user')))
                ->onlyForm()
            ,*/
            Checkbox::make('Показывать', 'is_active')->filter()->sortable()->fastEdit(),
            Datetime::make('Дата создания', 'created_at')->filter()->sortable()->default(Carbon::now()),
        ];
    }
}
