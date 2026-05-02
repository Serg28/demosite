<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnfinishedBasket extends Model
{
    protected $fillable = [
        'hash_basket',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(UnfinishedBasketsProducts::class);
    }

    public function saveProducts(array $items): void
    {
        $this->products()->delete();

        foreach ($items as $item) {
            $this->products()->create([
                'product_id' => $item['product_id'],
                'count' => $item['count'],
                'price' => $item['price'],
                'options' => $item['options'] ?? null,
            ]);
        }
    }
}
