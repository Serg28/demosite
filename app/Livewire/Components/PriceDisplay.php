<?php

namespace App\Livewire\Components;

use Livewire\Component;

class PriceDisplay extends Component
{
    public float $price = 0;
    public ?float $discountedPrice = null;
    public string $currency = 'UAH';
    public string $format = 'full'; // full, short, compact

    public function mount(?float $price = null, ?float $discountedPrice = null): void
    {
        if ($price !== null) {
            $this->price = $price;
        }
        if ($discountedPrice !== null) {
            $this->discountedPrice = $discountedPrice;
        }
    }

    public function getFormattedPrice(): string
    {
        return match($this->format) {
            'short' => number_format($this->price, 0) . ' ' . $this->currency,
            'compact' => number_format($this->price, 2, '.', '') . $this->currency,
            default => number_format($this->price, 2, ',', ' ') . ' ' . $this->currency,
        };
    }

    public function getDiscount(): float|null
    {
        if ($this->discountedPrice === null) {
            return null;
        }

        return round((1 - $this->discountedPrice / $this->price) * 100);
    }

    public function render()
    {
        return view('livewire.components.price-display');
    }
}
