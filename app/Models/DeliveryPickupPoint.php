<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryPickupPoint extends Model
{
    use HasTranslations;

    protected $table = 'delivery_pickup_points';

    public $timestamps = false;

    protected $guarded = [];

    protected $translatable = ['title', 'address'];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->orderBy('priority');
    }
}
