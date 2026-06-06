<?php

declare(strict_types=1);

namespace App\Livewire\Favorite;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Lists extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');
        }
    }

    #[Computed]
    public function products(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $user = Auth::user();
        $ids = $user->favoriteProductIds();

        return Product::query()
            ->whereIn('id', $ids)
            ->with('category')
            ->paginate(12);
    }

    #[Computed]
    public function totalCount(): int
    {
        return Auth::user()?->wishlists()->count() ?? 0;
    }

    #[On('updateFavoriteCount')]
    public function refreshList(): void
    {
        unset($this->products, $this->totalCount);
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.favorite.lists')
            ->layout('components.layouts.shop');
    }
}
