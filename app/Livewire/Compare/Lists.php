<?php

declare(strict_types=1);

namespace App\Livewire\Compare;

use App\Services\CompareService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Lists extends Component
{
    private CompareService $compare;

    public function boot(CompareService $compare): void
    {
        $this->compare = $compare;
    }

    #[Computed]
    public function products(): Collection
    {
        return $this->compare->products()->load('category');
    }

    #[Computed]
    public function totalCount(): int
    {
        return $this->compare->count();
    }

    public function clear(): void
    {
        $this->compare->clear();
        $this->dispatch('updateCompareCount', count: 0);
        unset($this->products, $this->totalCount);
    }

    #[On('updateCompareCount')]
    public function refresh(): void
    {
        unset($this->products, $this->totalCount);
    }

    public function render(): View
    {
        return view('livewire.compare.lists')
            ->layout('components.layouts.shop');
    }
}
