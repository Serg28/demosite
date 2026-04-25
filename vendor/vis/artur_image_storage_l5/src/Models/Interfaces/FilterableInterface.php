<?php namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Builder;

interface FilterableInterface
{
    public function scopeActive(Builder $query);

    public function scopeOrderId(Builder $query, $order = "desc");

    public function scopeFilterBySlug(Builder $query, $slug = '');

    public function scopeFilterByTitle(Builder $query, $title = '');

    public function scopeFilterByActivity(Builder $query, array $activity = []);

    public function scopeFilterByDate(Builder $query, array $date = []);

    public function scopeFilterByTags(Builder $query, array $tags = []);

    public function scopeFilterSearch(Builder $query);
}
