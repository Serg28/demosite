<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;

trait FilterableTrait
{
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', '1');
    }

    public function scopeOrderId(Builder $query, $order = "desc")
    {
        return $query->orderBy('id', $order);
    }

    public function scopeFilterBySlug(Builder $query, $slug = '')
    {
        if (!$slug) {
            return $query;
        }

        return $query->where('slug', $slug);
    }

    public function scopeFilterByTitle(Builder $query, $title = '')
    {
        if (!$title) {
            return $query;
        }

        return $query->where('title', 'like', '%' . $title . '%');
    }

    public function scopeFilterByActivity(Builder $query, array $activity = [])
    {
        if (!$activity) {
            return $query;
        }

        return $query->whereIn('is_active', $activity);
    }

    public function scopeFilterByDate(Builder $query, array $date = [])
    {
        if (!$date) {
            return $query;
        }

        $date['from'] = $date['from'] ?: '12-12-1971';
        $date['to']   = $date['to'] ?: '12-12-2222';

        $from = date('Y-m-d 00:00:00', strtotime($date['from']));
        $to   = date('Y-m-d 23:59:59', strtotime($date['to']));

        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeFilterByTags(Builder $query, array $tags = [])
    {
        if (!$tags) {
            return $query;
        }

        $relatedId = self::whereHas('tags', function (Builder  $query) use ($tags) {
            $query->whereIn('id_tag', $tags);
        })->pluck('id');

        return $query->whereIn('id', $relatedId);
    }

    public function scopeFilterSearch(Builder $query)
    {
        $filters = Session::get('image_storage_filter.' . $this->getConfigPrefix(), []);

        foreach ($filters as $column => $value) {
            if (is_callable([$this, $column])) {
                $query->$column($value);
            }
        }

        return $query;
    }

}
