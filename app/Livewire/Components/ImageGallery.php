<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ImageGallery extends Component
{
    public array $images = [];
    public int $selectedIndex = 0;

    public function mount(?array $images = null): void
    {
        if ($images !== null) {
            $this->images = $images;
        }
    }

    public function selectImage(int $index): void
    {
        if (isset($this->images[$index])) {
            $this->selectedIndex = $index;
        }
    }

    public function nextImage(): void
    {
        if ($this->selectedIndex < count($this->images) - 1) {
            $this->selectedIndex++;
        }
    }

    public function previousImage(): void
    {
        if ($this->selectedIndex > 0) {
            $this->selectedIndex--;
        }
    }

    public function render()
    {
        return view('livewire.components.image-gallery');
    }
}
