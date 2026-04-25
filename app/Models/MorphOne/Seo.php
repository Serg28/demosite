<?php

namespace App\Models\MorphOne;

use App\Traits\PrepareModelFields;
use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Fields\Checkbox;
use Vis\Builder\Fields\Froala;
use Vis\Builder\Fields\Text;
use Vis\Builder\Fields\Textarea;
use Vis\Builder\Helpers\Traits\TranslateTrait;

class Seo extends Model
{
    use TranslateTrait;
    use PrepareModelFields;

    protected $table = 'seo';

    protected $guarded = [];

    public $timestamps = false;

    public static function fieldsForDefinitions()
    {
        return [
            Text::make('Seo H1', 'seo_h1')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Text::make('Seo Title', 'seo_title')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Textarea::make('Seo Description', 'seo_description')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Froala::make('Seo текст', 'seo_text')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Text::make('Seo canonical', 'seo_canonical')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Text::make('Seo Keywords', 'seo_keywords')
                ->language()
                ->morphOne('seo')
                ->onlyForm(),
            Checkbox::make('Seo noindex', 'is_seo_noindex')
                ->onlyForm()
                ->morphOne('seo'),
        ];
    }
}
