<?php

declare(strict_types=1);

namespace App\Livewire\Favorite;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Count extends Component
{
    #[Computed]
    public function count(): int
    {
        return Auth::user()?->wishlists()->count() ?? 0;
    }

    #[On('updateFavoriteCount')]
    public function refresh(): void
    {
        unset($this->count);
    }

    public function render(): View
    {
        return view('livewire.favorite.count');
    }
}
