<?php

namespace App\Models;

class NPStreet extends BaseModel
{
    protected $table = 'np_streets';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    //Атрибут ref, содержащий уникальный UUID в справочнике перевозчика.
    public function getRefAttribute(): ?string
    {
        return $this->attributes['ref'] ?? null;
    }
}
