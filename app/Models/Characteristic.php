<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Characteristic extends BaseModel
{
    protected $table = 'characteristics';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function getUrl($locale = ''): string
    {
        //return route('characteristic', $this->slug); // из коробки: урл = slug
        return route('characteristic', $this->getUrlOrSlug($locale)); //мультиязычный урл
    }

    public function getSlug($replacer = "-", $pattern = "a-z0-9"): string
    {
        return \Str::slug(strip_tags($this->id_1c), $replacer, $pattern);
    }

    public function options(): HasMany
    {
        return $this->hasMany(CharacteristicOption::class)->orderPriority()->orderBy(
            'title->' . App::getLocale(),
            'asc'
        );
    }

    public function optionsCache()
    {
        return $this->options()->remember(20 * 60)->cacheTags(['characteristics', 'characteristic_options']);
    }

    public function optionsCacheFiltered($ids)
    {
        $options = $this->optionsCache();

        if ($ids) {
            return $options->whereIn('id', $ids)->get();
        }

        return $options->get();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function characteristicTitle()
    {
        if ($title = $this->pivot?->name) {
            $title = $this->getTextFromJson($title);
        }

        return ($title) ?: $this->t('title');
    }

    public function scopeFilterable($query)
    {
        return $query->where('category_characteristic.is_filter', 1);
    }

    public function getTextFromJson(string $text, string $lang = ''): string
    {
        $lang = ($lang) ?: App::getLocale();
        $decode = json_decode($text);

        if (!$decode) {
            return '';
        }

        return $decode->$lang;
    }
}
