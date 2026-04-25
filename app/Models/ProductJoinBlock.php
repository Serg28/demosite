<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductJoinBlock extends BaseModel
{
    protected $table = 'product_join_blocks';

    protected $fillable = [];

    public $timestamps = false;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_join_products',
            'product_join_block_id',
            'product_id'
        );
    }
}
