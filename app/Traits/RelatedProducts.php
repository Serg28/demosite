<?php

namespace App\Traits;

use App\Models\CharacteristicRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait RelatedProducts
{
    public function characteristicRelations(): BelongsToMany
    {
        return $this->belongsToMany(
            CharacteristicRelation::class,
            'product_characteristic_relation',
            'product_id',
            'relation_id',
            'id',
            'id'
        );
    }

    public function getRelatedProductsAttribute(): Collection
    {
        return $this->characteristicRelations()
            ->with(['products' => fn (BelongsToMany $load) => $load->where('products.id', '!=', $this->id)])
            ->get();
    }
}
