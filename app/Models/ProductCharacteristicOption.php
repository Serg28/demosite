<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCharacteristicOption extends BaseModel
{
    protected $table = 'product_characteristic_options';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    public function characteristicOption(): BelongsTo
    {
        return $this->belongsTo(CharacteristicOption::class);
    }
}
