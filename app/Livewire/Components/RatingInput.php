<?php

namespace App\Livewire\Components;

use Livewire\Component;

class RatingInput extends Component
{
    public int $rating = 0;
    public int $hoverRating = 0;
    public int $maxRating = 5;

    public function setRating(int $rating): void
    {
        if ($rating >= 1 && $rating <= $this->maxRating) {
            $this->rating = $rating;
            $this->dispatch('ratingChanged', rating: $rating);
        }
    }

    public function setHoverRating(int $rating): void
    {
        $this->hoverRating = $rating;
    }

    public function clearHover(): void
    {
        $this->hoverRating = 0;
    }

    public function render()
    {
        return view('livewire.components.rating-input');
    }
}
