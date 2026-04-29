<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

/**
 * Provides locale-aware URL resolution via per-locale virtual URL columns
 * (ua_url, ru_url, en_url) with fallback to the shared `slug` column.
 *
 * Compatible interface with linecore-demo SlugUrlFieldTrait.
 */
trait SlugUrlFieldTrait
{
    /**
     * Returns locale-specific virtual URL if set, otherwise the slug.
     */
    public function getUrlOrSlug(string $locale = ''): string
    {
        return $this->getVirtualUrlByLocale($locale) ?? $this->slug ?? '';
    }

    /**
     * Returns the {locale}_url attribute value, or null if not set.
     */
    protected function getVirtualUrlByLocale(string $locale = ''): ?string
    {
        $locale = $locale ?: App::getLocale();
        $value  = $this->getAttribute("{$locale}_url");

        return filled($value) ? $value : null;
    }

    /**
     * Find a model by its locale URL alias or slug.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSlug(Builder $query, string $slug, ?string $table = null): Builder
    {
        if (empty($slug)) {
            return $query;
        }

        $locale    = App::getLocale();
        $prefix    = $table ? "{$table}." : '';
        $urlField  = "{$prefix}{$locale}_url";
        $slugField = "{$prefix}slug";

        return $query->where(
            fn (Builder $q) => $q
                ->where(fn (Builder $q) => $q->whereNotNull($urlField)->where($urlField, $slug))
                ->orWhere(fn (Builder $q) => $q->where(fn (Builder $q) => $q->whereNull($urlField)->orWhere($urlField, ''))->where($slugField, $slug))
        );
    }

    /**
     * Find models by multiple locale URL aliases or slugs.
     *
     * @param  Builder<static>  $query
     * @param  string[]  $slugs
     */
    public function scopeSlugIn(Builder $query, array $slugs, ?string $table = null): Builder
    {
        if (empty($slugs)) {
            return $query;
        }

        $locale    = App::getLocale();
        $prefix    = $table ? "{$table}." : '';
        $urlField  = "{$prefix}{$locale}_url";
        $slugField = "{$prefix}slug";

        return $query->where(
            fn (Builder $q) => $q
                ->where(fn (Builder $q) => $q->whereNotNull($urlField)->whereIn($urlField, $slugs))
                ->orWhere(fn (Builder $q) => $q->where(fn (Builder $q) => $q->whereNull($urlField)->orWhere($urlField, ''))->whereIn($slugField, $slugs))
        );
    }

    /**
     * Returns the full localized URL of the current request, optionally
     * replacing the current page's path with another locale's path.
     * Used for hreflang alternate links.
     */
    public function getFullUrl(string $locale = ''): string
    {
        if (! $locale) {
            return request()->fullUrl();
        }

        $current   = $this->getUrl();
        $localized = $this->getUrl($locale);

        return str_replace($current, $localized, request()->fullUrl());
    }

    public function getLocalizedSlugAttribute(): string
    {
        return $this->getUrlOrSlug();
    }
}
