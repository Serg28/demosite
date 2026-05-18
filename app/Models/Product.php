<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use App\Models\Traits\SlugUrlFieldTrait;
use App\Traits\AnaliticProductTrait;
use App\ValueObjects\PriceTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use AnaliticProductTrait, HasFactory, HasTranslations, Searchable, SlugUrlFieldTrait;

    /** @var string[] */
    protected array $cardFields = [
        'id', 'title', 'code', 'price', 'price_old', 'picture',
        'is_active', 'quantity', 'slug', 'ua_url', 'ru_url', 'en_url',
        'category_id', // потрібен для eager-load category → getWholesalePrices()
    ];

    /** @var string[] */
    protected array $cartFields = ['id', 'title', 'picture'];

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
        'quantity',
        'weight',
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
        'quantity' => 'integer',
        'weight'   => 'decimal:3',
    ];

    public function scopeCardFields($query): void
    {
        $query->select($this->cardFields);
    }

    public function scopeCartFields($query): void
    {
        $query->select($this->cartFields);
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    public function scopeAvailable($query): void
    {
        $query->where('is_active', true)->where('quantity', '>', 0);
    }

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

    public function interestingProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'products_interesting_products',
            'product_id',
            'similar_id'
        );
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
        return geturl('/product/'.$this->getUrlOrSlug($locale), $locale ?: null);
    }

    public function getArticle(): string
    {
        return $this->code ?? (string) $this->id;
    }

    public function getPrice(): float
    {
        return (float) $this->price;
    }

    public function getPriceOld(): ?float
    {
        return $this->price_old ? (float) $this->price_old : null;
    }

    public function hasDiscount(): bool
    {
        $old = $this->getPriceOld();

        return $old !== null && $old > $this->getPrice();
    }

    public function getPriceDifferencePercentage(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((1 - $this->getPrice() / $this->getPriceOld()) * 100);
    }

    public function getWeight(): float
    {
        return (float) ($this->weight ?? 0);
    }

    public function getQuantity(): int
    {
        return $this->quantity ?? 9999;
    }

    public function isActiveForOrder(): bool
    {
        return $this->is_active && $this->getQuantity() > 0;
    }

    public function isAvailability(): bool
    {
        return false;
    }

    public function isActiveForPreOrder(): bool
    {
        return false;
    }

    /**
     * Оптові ціни за рівнями кількості (з категорії товару).
     * Формат: [min_qty => price_per_unit]
     *
     * @return array<int, float>|null
     */
    public function getWholesalePrices(): ?array
    {
        $tiers = $this->category?->getWholesaleTiers();

        if (! $tiers || $tiers->isEmpty()) {
            return null;
        }

        $base = $this->getPrice();

        return $tiers->mapWithKeys(
            fn (PriceTier $tier) => [$tier->minQty => $tier->priceFor($base)]
        )->all();
    }

}
