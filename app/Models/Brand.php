<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Brand extends BaseModel
{
    protected $table = 'brands';

    protected $fillable = [];

    public $timestamps = false;

    public function getUrl($locale = ''): string
    {
        return geturl('/brand/' . $this->slug);
    }

    public function characteristic_brand(): HasOne
    {
        return $this->HasOne(CharacteristicOption::class);
    }
}
