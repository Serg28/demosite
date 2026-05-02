<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnfinishedBasketsProducts extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'unfinished_basket_id',
        'product_id',
        'count',
        'price',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'price' => 'decimal:2',
        ];
    }

    public function basket(): BelongsTo
    {
        return $this->belongsTo(UnfinishedBasket::class, 'unfinished_basket_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
