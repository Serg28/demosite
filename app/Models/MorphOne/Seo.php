<?php

namespace App\Models\MorphOne;

use Illuminate\Database\Eloquent\Model;
use Linecore\Cms\Fields\Checkbox;
use Linecore\Cms\Fields\Froala;
use Linecore\Cms\Fields\Text;
use Linecore\Cms\Fields\Textarea;
use Linecore\Cms\Helpers\Traits\TranslateTrait;

class Seo extends Model
{
    use TranslateTrait;

    protected $table = 'seo';

    protected $guarded = [];

    public $timestamps = false;

    public static function fieldsForDefinitions(): array
    {
        return [
            Text::make('Seo title', 'seo_title')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Textarea::make('Seo description', 'seo_description')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Text::make('Seo keywords', 'seo_keywords')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Checkbox::make('Seo noindex', 'is_seo_noindex')
                ->morphOne('seo')
                ->onlyForm(),
            Froala::make('Seo текст', 'seo_text')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
        ];
    }
}
