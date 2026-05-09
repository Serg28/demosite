<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NPWarehouse extends Model
{
    use HasTranslations;

    protected $table = 'np_warehouses';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
