<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;

class Gallery extends Component
{
    public int $productId;
    public array $images = [];
    public int $selectedIndex = 0;
    public ?string $youtubeId = null;
    public bool $showVideo = false;

    public function mount(Product $product): void
    {
        $this->productId = $product->id;

        $images = [];

        if ($product->picture) {
            $images[] = $product->picture;
        }

        if (! empty($product->other_pictures)) {
            foreach ($product->other_pictures as $img) {
                if ($img && $img !== $product->picture) {
                    $images[] = $img;
                }
            }
        }

        $this->images = $images;
        $this->youtubeId = $this->extractYoutubeId($product->t('link_to_youtube'));
    }

    public function selectImage(int $index): void
    {
        if (isset($this->images[$index])) {
            $this->selectedIndex = $index;
            $this->showVideo = false;
        }
    }

    public function selectVideo(): void
    {
        $this->showVideo = true;
    }

    public function nextImage(): void
    {
        if ($this->selectedIndex < count($this->images) - 1) {
            $this->selectedIndex++;
            $this->showVideo = false;
        }
    }

    public function previousImage(): void
    {
        if ($this->selectedIndex > 0) {
            $this->selectedIndex--;
            $this->showVideo = false;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.product.gallery');
    }

    private function extractYoutubeId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }

        return null;
    }
}
