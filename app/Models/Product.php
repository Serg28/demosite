<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'short_description',
        'price',
        'price_old',
        'status',
        'picture',
        'other_pictures',
        'link_to_youtube',
        'is_active',
        'category_id',
        'slug',
        'external_id',
        'analogs',
        'other_categories',
        'priority',
    ];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'short_description' => 'json',
        'price' => 'decimal:2',
        'price_old' => 'decimal:2',
        'is_active' => 'boolean',
        'other_pictures' => 'json',
        'link_to_youtube' => 'json',
        'analogs' => 'json',
        'other_categories' => 'json',
        'priority' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

}
