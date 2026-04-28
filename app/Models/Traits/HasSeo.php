<?php

namespace App\Models\Traits;

use App\Models\MorphOne\Seo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * SEO methods for models with morphOne Seo relationship.
 * API matches linecore-demo SeoTrait (getSeoTitle, getSeoCanonical, etc.)
 * without SeoGroups/SeoUrls/placeholder complexity.
 */
trait HasSeo
{
    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seo');
    }

    public function getSeoTitle(): string
    {
        $value = $this->seo?->t('seo_title');
        if (empty($value)) {
            $value = method_exists($this, 't') ? $this->t('title') : ($this->title ?? '');
        }

        return strip_tags((string) $value);
    }

    public function getSeoDescription(): string
    {
        $value = $this->seo?->t('seo_description');
        if (empty($value)) {
            $value = method_exists($this, 't') ? ($this->t('short_description') ?? '') : '';
        }

        return strip_tags((string) $value);
    }

    public function getSeoH1(): string
    {
        $value = $this->seo?->t('seo_h1');
        if (empty($value)) {
            $value = method_exists($this, 't') ? $this->t('title') : ($this->title ?? '');
        }

        return strip_tags((string) $value);
    }

    public function getSeoKeywords(): string
    {
        return strip_tags((string) ($this->seo?->t('seo_keywords') ?? ''));
    }

    public function getSeoText(): string
    {
        return (string) ($this->seo?->t('seo_text') ?? '');
    }

    public function getSeoPicture(): string
    {
        if (! empty($this->picture)) {
            return asset(glide($this->picture, ['w' => 600, 'h' => 600]));
        }

        return asset(glide(setting('seo_logo'), ['w' => 600, 'h' => 600]));
    }

    public function getSeoCanonical(): string
    {
        $value = $this->seo?->t('seo_canonical');

        return strip_tags($value ?: currentUrl());
    }

    public function getSeoNoindex(): bool
    {
        return (bool) ($this->seo?->is_seo_noindex ?? false);
    }

    public function getSeoNofollow(): bool
    {
        return (bool) ($this->seo?->is_seo_nofollow ?? false);
    }

    public function getRobotsMetaTag(): ?string
    {
        $parts = [
            $this->getSeoNoindex() ? 'noindex' : 'index',
            $this->getSeoNofollow() ? 'nofollow' : 'follow',
        ];

        return $parts === ['index', 'follow'] ? null : implode(', ', $parts);
    }
}
