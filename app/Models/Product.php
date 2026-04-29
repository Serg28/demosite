<?php

namespace App\Models;

use App\Models\Traits\SlugUrlFieldTrait;
use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, SlugUrlFieldTrait, HasTranslations, Searchable;

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
        'ua_url',
        'ru_url',
        'en_url',
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
            'id' => (string) $this->id,
            'title' => $this->title['ua'] ?? $this->title['ru'] ?? '',
            'description' => $this->description['ua'] ?? $this->description['ru'] ?? '',
            'price' => (float) $this->price,
            'price_old' => (float) ($this->price_old ?? 0),
            'category_id' => (int) ($this->category_id ?? 0),
            'slug' => (string) ($this->slug ?? ''),
            'is_active' => (bool) $this->is_active,
            'priority' => (int) ($this->priority ?? 0),
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

    public function getUrl(string $locale = ''): string
    {
        return geturl('/product/' . $this->getUrlOrSlug($locale), $locale ?: null);
    }
}
