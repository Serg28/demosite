<?php

namespace App\Cms\Definitions;

use App\Cms\Fields\ManyToManyCategories;
use App\Cms\Fields\ManyToManyOptionsXml;
use App\Cms\Fields\SelectFeed;
use App\Enums\FeedTypeEnum;
use App\Models\Feed;
use Vis\Builder\Definitions\Resource;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\ForeignAjax;
use Vis\Builder\Fields\Id;
use Vis\Builder\Fields\Number;
use Vis\Builder\Fields\Relations\Options;
use Vis\Builder\Fields\Select;
use Vis\Builder\Fields\Text;
use Vis\Builder\Services\Actions;

class XmlFeeds extends Resource
{
    public $model = Feed::class;

    public $title = 'XML фиды';

    public function fields(): array
    {
        return [
            'Основное' => [
                Id::make('#', 'id')->sortable(),

                SelectFeed::make('Фид', 'feed_name')
                    ->options($this->getFeeds())
                    ->filter(),
                Text::make('Описание фида', 'description')->language()->onlyForm(),
                Number::make('Лимит товаров в фиде (0 - без ограничения)', 'limit')->onlyForm()->default(0),
                Select::make('Отображать', 'type')->options(FeedTypeEnum::toArray())->action()->onlyForm(),

                ManyToManyCategories::make('Категории в xml файле')
                    ->options(
                        (new Options('categories'))->keyField('description')
                    )->className('categories'),

                ForeignAjax::make('Характеристика для фильтрации', 'characteristic_id')
                    ->options((new Options('characteristic'))
                        ->isJson()),

                ManyToManyOptionsXml::make('Опции для фильтрации')
                    ->options(
                        (new Options('options'))->where('characteristic_id', '=', $this->getThisCharacteristicId())
                    ),

                Checkbox::make('Активный', 'is_active')->fastEdit(),
            ],
        ];
    }

    public function actions()
    {
        return Actions::make()->insert()->update()->preview()->clone()->revisions()->delete();
    }

    protected function getFeeds(): array
    {
        $feedClasses = [];

        $xmlClassFeeds = new \FilesystemIterator(app_path('Services/Xml'), \FilesystemIterator::SKIP_DOTS);

        foreach ($xmlClassFeeds as $feed) {
            $className = $feed->getBaseName('.php');
            $feedClasses[strtolower($className)] = $className;
        }

        return $feedClasses;
    }

    private function getThisCharacteristicId()
    {
        if (request()->has('id')) {
            return Feed::find(request()->get('id'))?->characteristic_id ?: 0;
        }

        return 0;
    }
}
