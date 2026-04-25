<?php

namespace App\Traits;

use App\Models\MorphOne\Rating;

trait RatingTrait
{
    public function saveRating(float $rating): void
    {
        $rating = Rating::create([
            'ip' => request()->ip(),
            'rating' => $rating,
        ]);

        $this->rating()->save($rating);

        cache()->tags($this->getCacheTag())->flush();
    }

    public function checkExistsRating(): bool
    {
        return $this->rating()->where('ip', request()->ip())->exists();
    }

    public function getRating(): float
    {
        return $this->rating()
            ->rememberForever()
            ->cacheTags($this->getCacheTag())
            ->avg('rating');
    }

    private function getCacheTag(): string
    {
        return 'rating_'.$this->id;
    }
}
