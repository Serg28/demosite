<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'brand_id',
        'slug',
        'external_id',
        'analogs',
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
        'priority' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(
            Characteristic::class,
            'product_characteristic_options',
            'product_id',
            'characteristic_option_id'
        )->withPivot('characteristic_option_id');
    }

    /**
     * Получить данные для TypeSense индексирования.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title['ua'] ?? $this->title['ru'] ?? '',
            'description' => $this->description['ua'] ?? $this->description['ru'] ?? '',
            'price' => (float) $this->price,
            'price_old' => (float) ($this->price_old ?? 0),
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'priority' => $this->priority,
            'created_at' => $this->created_at?->timestamp ?? 0,
        ];
    }

    /**
     * Условие для индексирования (только активные товары).
     */
    public function shouldBeSearchable(): bool
    {
        return $this->is_active;
    }
}
