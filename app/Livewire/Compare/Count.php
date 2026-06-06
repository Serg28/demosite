<?php

declare(strict_types=1);

namespace App\Livewire\Compare;

use App\Services\CompareService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Count extends Component
{
    private CompareService $compare;

    public function boot(CompareService $compare): void
    {
        $this->compare = $compare;
    }

    #[Computed]
    public function count(): int
    {
        return $this->compare->count();
    }

    #[On('updateCompareCount')]
    public function refresh(): void
    {
        unset($this->count);
    }

    public function render(): View
    {
        return view('livewire.compare.count');
    }
}
