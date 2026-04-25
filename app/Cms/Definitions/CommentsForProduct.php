<?php

namespace App\Cms\Definitions;

use App\Models\Comment;
use Carbon\Carbon;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;

class CommentsForProduct extends Resource
{
    public $model = Comment::class;

    public $title = 'Комментарии';

    protected $cacheTag = 'comments';

    public function fields(): array
    {
        return [
            Id::make('#', 'id')->sortable(),
            //Hidden::make('Продукт', 'product_id')
            Hidden::make('Продукт', 'commentable_id')
                ->onlyForm()
                ->default(request('foreign_field_id')),
            Hidden::make('Продукт', 'commentable_type')->onlyForm()->default('product'),

            Text::make('Имя', 'name')->filter()->sortable(),
            Textarea::make('Сообщение', 'body')->filter()->sortable(),
            //Textarea::make('Ответ', 'answer')->onlyForm(),
            Number::make('Рейтинг', 'rating')->filter()->sortable(),
            //Text::make('parent_id', 'parent_id')->onlyForm()->default(null),
            /*ForeignAjax::make('Пользователь', 'user_id')
                ->options(
                    (new Options('user')))
                ->onlyForm()
            ,*/
            Checkbox::make('Показывать', 'is_active')->filter()->sortable(),
            Datetime::make('Дата создания', 'created_at')->filter()->sortable()->default(Carbon::now()),
        ];
    }
}
