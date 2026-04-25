<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CharacteristicOption extends BaseModel
{
    protected $table = 'characteristic_options';

    public $timestamps = false;

    protected $with = ['characteristic'];

    public function getUrl($locale = ''): string
    {
        //return route('characteristic_option', [$this->characteristic->slug, $this->slug]); // из коробки: урл = slug
        //мультиязычный урл
        return route(
            'characteristic_option',
            [$this->characteristic->getUrlOrSlug($locale), $this->getUrlOrSlug($locale)]
        );
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function characteristicCache(): Characteristic
    {
        return $this->characteristic()
            ->remember(20 * 60)->cacheTags(['characteristics', 'characteristic_options'])
            ->first();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_characteristic_options',
            'characteristic_option_id',
            'product_id'
        );
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Brand::class, 'characteristic_option_id', 'id');
    }
}
