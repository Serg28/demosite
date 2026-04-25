<?php

namespace App\Models;

use App\Enums\FeedTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Events\FeedSaved;
use Illuminate\Support\Facades\App;

class Feed extends BaseModel
{
    protected $dispatchesEvents = [
        'saved' => FeedSaved::class,
        'deleted' => FeedSaved::class,
    ];

    public FeedTypeEnum $typeEnum;

    /**
     * Переименование поля feed_name при клонировании модели
     *
     * @param  array|null  $except
     */
    public function replicate(array $except = null)
    {
        $clone = parent::replicate($except);
        $clone->feed_name = $clone->feed_name . '-' . time();

        return $clone;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'feed_category');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(CharacteristicOption::class, 'feed_characteristic_options');
    }

    //Ссылка на динамический файл (из коробки)
    /*public function getUrl(): string
    {
        return route('xml_feed', ['feed_name' => $this->feed_name, 'id' => $this->id]);
    }*/

    //Ссылка на статический файл
    public function getUrl($locale = false): string
    {
        $locale = ($locale) ?: App::getLocale();
        return route('xml_feed_file', ['feed_file' => $locale . '-' . $this->feed_name . '-' . $this->id]);
    }
}
