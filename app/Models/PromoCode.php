<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends BaseModel
{
    protected $table = 'promo_codes';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    public function applicableProducts()
    {
        return $this->belongsToMany(Product::class, 'promo_code_product', 'promo_code_id', 'product_code', '', 'code')->withPivot('product_code');;
    }

    public function product_promocode(): HasMany
    {
        return $this->hasMany(ProductPromocode::class, 'promo_code_id');
    }
}
