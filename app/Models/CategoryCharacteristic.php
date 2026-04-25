<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryCharacteristic extends BaseModel
{
    protected $table = 'category_characteristic';

    protected $fillable = [];

    public $timestamps = false;

    public function characteristic(): belongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function group(): belongsTo
    {
        return $this->belongsTo(CharacteristicGroupName::class, 'group_id');
    }

    //Характеристика доступна для фильтра
    public function scopeFilterable($query)
    {
        return $query->where('category_characteristic.is_filter', 1);
    }
}
