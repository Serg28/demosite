<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacteristicOption_ extends BaseModel
{
    protected $table = 'characteristic_options';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    protected $with = ['characteristic'];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function characteristicCache(): Characteristic
    {
        return $this->characteristic()
            ->rememberForever()->cacheTags(['characteristics', 'characteristic_options'])
            ->first();
    }
}
