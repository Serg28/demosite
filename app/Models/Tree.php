<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Linecore\Cms\Tree as TreeBuilder;

class Tree extends TreeBuilder
{
    public static function getFirstDepthNodes(): Collection
    {
        return self::where('depth', '1')->get();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', '1');
    }

    public function scopePriorityAsc(Builder $query): Builder
    {
        return $query->orderBy('lft', 'asc');
    }

    public function scopeTemplate(Builder $query, string $template): Builder
    {
        return $query->where('template', $template);
    }

    public function getUrl(string $locale = ''): ?string
    {
        $locale = $locale ?: App::getLocale();

        return geturl('/'.parent::getUrl(), $locale);
    }
}
