<?php

namespace App\Traits;

use Conner\Likeable\Like;
use Conner\Likeable\LikeCounter;

trait LikeableTrait
{
    private string $cacheTag = 'like';

    public static function bootLikeable(): void
    {
        if (static::removeLikesOnDelete()) {
            static::deleting(function ($model): void {
                $model->removeLikes();
            });
        }
    }

    public function scopeWhereLikedBy($query, $userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('likes', function ($q) use ($userId): void {
            $q->where('user_id', '=', $userId);
        });
    }

    public function scopeWhereLikedByPrice($query, $userId = null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('likes', function ($q) use ($userId): void {
            $q->where('user_id', '=', $userId);
        })->select('products.*'); // Выбираем нужные колонки, включая цену
    }

    public function getLikeCountAttribute(): int
    {
        return $this->likeCounter ? $this->likeCounter->count : 0;
    }

    public function like(int $userId = null): void
    {
        $userId = $this->getUser($userId);

        if ($userId) {
            cache()->forget($this->getCacheTag($userId));

            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();

            if ($like) {
                return;
            }

            $like = new Like();
            $like->user_id = $userId;
            $this->likes()->save($like);
        }

        $this->incrementLikeCount();
    }

    public function unlike(int $userId = null): void
    {
        $userId = $this->getUser($userId);

        if ($userId) {
            cache()->forget($this->getCacheTag($userId));

            $like = $this->likes()
                ->where('user_id', '=', $userId)
                ->first();

            if (! $like) {
                return;
            }

            $like->delete();
        }

        $this->decrementLikeCount();
    }

    public function liked($userId = null)
    {
        $userId = $this->getUser($userId);

        $model = $this;

        return cache()->rememberForever($this->getCacheTag($userId), function () use ($userId, $model) {
            return (bool) $model->likes()
                ->where('user_id', '=', $userId)
                ->count();
        });
    }

    public static function removeLikesOnDelete()
    {
        return static::$removeLikesOnDelete ?? true;
    }

    public function removeLikes(): void
    {
        $this->likes()->delete();
        $this->likeCounter()->delete();
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getLikedAttribute()
    {
        return $this->liked();
    }

    public function likeCounter()
    {
        return $this->morphOne(LikeCounter::class, 'likeable');
    }

    private function getUser(?int $userId): int
    {
        return is_null($userId) ? $this->loggedInUserId() : $userId;
    }

    private function incrementLikeCount(): void
    {
        $counter = $this->likeCounter()->first();

        if ($counter) {
            $counter->count++;
            $counter->save();
        } else {
            $counter = new LikeCounter();
            $counter->count = 1;
            $this->likeCounter()->save($counter);
        }
    }

    private function decrementLikeCount(): void
    {
        $counter = $this->likeCounter()->first();

        if ($counter) {
            $counter->count--;
            if ($counter->count) {
                $counter->save();
            } else {
                $counter->delete();
            }
        }
    }

    private function loggedInUserId()
    {
        return auth()->id();
    }

    private function getCacheTag(int $userId): string
    {
        return 'like'.$userId.$this->id;
    }
}
