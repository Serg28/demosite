<?php

namespace App\Cms\Tree\Templates;

use Linecore\Cms\Definitions\ResourceTree;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Froala;
use Linecore\Cms\Fields\Id;
use Linecore\Cms\Fields\Image;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Fields\Textarea;

class Contacts extends ResourceTree
{
    protected $titleDefinition = 'Контакти';

    public function fields(): array
    {
        return [
            'Загальне' => [
                Id::make('#', 'id')->sortable(),
                Text::make(__cms('Заголовок'), 'title')->language(),
                Froala::make(__cms('Опис'), 'description')->language(),
                Text::make('URL', 'slug'),
                Image::make(__cms('Зображення'), 'picture'),
                Checkbox::make(__cms('Активно'), 'is_active'),
            ],
            'SEO' => [
                Text::make('SEO title', 'seo_title'),
                Textarea::make('SEO description', 'seo_description'),
            ],
        ];
    }
}
