<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ForeignBlog;
use App\Models\MorphOne\Seo;
use App\Models\News as NewsModel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Datetime;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Image;
use Vis\Builder\Fields\ManyToMany;
use Vis\Builder\Fields\ReadonlyField;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Services\Actions;

class News extends Resource
{
    public $model = NewsModel::class;

    public $title = 'Новости';

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),

                Text::make('Заголовок', 'title')
                    ->language()
                    ->filter()
                    ->sortable()
                    ->transliteration('slug', true),

                Text::make('Url', 'slug')
                    ->filter()
                    ->sortable()->rules([
                        'required',
                        Rule::unique('news')->ignore(request('id')),
                    ]),

                Froala::make('Описание', 'description')->language()->onlyForm(),
                Textarea::make('Короткое описание', 'short_description')->language()->onlyForm()->onlyForm(),

                Image::make('Фото', 'picture')->filter()->sortable(),
                Checkbox::make('Активно', 'is_active')->filter()->sortable(),
                Datetime::make('Дата создания', 'created_at')->filter()->sortable()->default(Carbon::now()),

                ReadonlyField::make('Кол. просмотров', 'count_view')->filter()->sortable(),

                ForeignBlog::make('Автор', 'user_id')
                    ->options((new Options('author'))
                        ->keyField('first_name'))
                    ->filter(),

                ManyToMany::make('Теги')
                    ->options(
                        (new Options('tags'))
                    ),
                //--

                Select::make('Категория новости', 'tree_id')
                    ->options($this->model()->getTreeForMenu(10)),

                //--
            ],

            'SEO' => Seo::fieldsForDefinitions(),
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->clone()->revisions()->delete();
    }
}
