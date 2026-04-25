<?php

namespace App\Cms\Definitions;

use App\Models\Setting;
use Illuminate\Validation\Rule;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Custom\TextSetting;
use Vis\Builder\Fields\File;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Hidden;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Services\Actions;

class Settings extends Resource
{
    public $model = Setting::class;

    public $title = 'Настройки';

    protected $orderBy = 'id desc';

    protected $cacheTag = 'settings';

    public function fields()
    {
        return [

            Hidden::make('group', 'group')->default('general'),
            Text::make('Название', 'title')
                ->filter()->sortable()
                ->transliteration('slug', true),
            Text::make('Код для вставки', 'slug')->filter()->sortable()
                ->rules([
                    'required',
                    Rule::unique('settings')->ignore(request('id')),
                ]),

            Select::make('Группа', 'group')->options($this->model()->getGroups())->onlyForm(),

            Select::make('Тип', 'type')
                ->options([
                    'text' => 'Текст',
                    'text_with_languages' => 'Текст с переводами',
                    'textarea_with_languages' => 'Большой текст с переводами',
                    'froala_with_languages' => 'Текстовый редактор с переводами',
                    'file' => 'Файл',
                    'checkbox' => 'Вкл/Выкл',
                ])->action(),

            TextSetting::make('Текст', 'value')
                ->className('text'),

            Text::make('Текст с переводами', 'value_languages')->language()->onlyForm()
                ->className('text_with_languages'),

            Textarea::make('Большой текст с переводами', 'textarea_with_languages')->language()->onlyForm()
                ->className('textarea_with_languages'),

            Froala::make('Текстовый редактор с переводами', 'froala_with_languages')->language()->onlyForm()
                ->className('froala_with_languages'),

            File::make('Файл', 'file')->onlyForm()
                ->className('file'),

            Checkbox::make('Включить', 'check')->onlyForm()
                ->className('checkbox'),
        ];
    }

    public function actions()
    {
        return Actions::make()->update()->insert();
    }
}
