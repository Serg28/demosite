<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnfinishedBasketsProducts extends BaseModel
{
    protected $table = 'unfinished_baskets_products';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function unfinishedBasket(): BelongsTo
    {
        return $this->belongsTo(UnfinishedBasket::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
