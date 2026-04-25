<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends BaseModel
{
    protected $table = 'regions';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
