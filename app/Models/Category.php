<?php

namespace App\Models;

use App\Models\Traits\HasSeo;
use App\Models\Traits\HasTranslations;
use App\Models\Traits\SlugUrlFieldTrait;
use App\ValueObjects\PriceTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory, HasSeo, HasTranslations, SlugUrlFieldTrait;

    public function getUrl(string $locale = ''): string
    {
        return geturl('/catalog/' . $this->getUrlOrSlug($locale), $locale ?: null);
    }

    /** Canonical fallback: explicit from DB or clean category URL (without filter segments). */
    public function getSeoCanonical(): string
    {
        $value = $this->seo?->t('seo_canonical');

        return strip_tags($value ?: $this->getUrl());
    }

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'depth',
        'title',
        'slug',
        'picture',
        'is_active',
    ];

    protected $casts = [
        'title'            => 'json',
        'is_active'        => 'boolean',
        'depth'            => 'integer',
        'lft'              => 'integer',
        'rgt'              => 'integer',
        'wholesale_tiers'  => 'json',
    ];

    /** @return Collection<int, PriceTier> */
    public function getWholesaleTiers(): Collection
    {
        return collect($this->wholesale_tiers ?? [])
            ->map(fn (array $tier) => PriceTier::from($tier))
            ->sortBy('minQty')
            ->values();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
