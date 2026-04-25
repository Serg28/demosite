<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnfinishedBasket extends BaseModel
{
    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(UnfinishedBasketsProducts::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saveProducts(array $products = []): void
    {
        $this->touch();

        $this->products()->delete();
        $this->products()->saveMany($products);
    }
}
