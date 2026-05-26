<?php

namespace App\Livewire\Profile\Discount;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Lists extends Component
{
    #[Computed]
    public function discountCards(): \Illuminate\Database\Eloquent\Collection
    {
        return Auth::user()->discountCards()->where('is_active', true)->orderByDesc('id')->get();
    }

    #[Computed]
    public function bestDiscount(): int
    {
        $cardDiscount      = (int) ($this->discountCards->where('type', 'percent')->max('value') ?? 0);
        $cumulativeDiscount = Auth::user()->effectiveDiscount();

        return max($cardDiscount, $cumulativeDiscount);
    }

    #[Computed]
    public function cumulativeDiscount(): int
    {
        return Auth::user()->effectiveDiscount();
    }

    #[Computed]
    public function cumulativeLevels(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\CumulativeDiscount::query()->orderBy('total_sum')->get();
    }

    #[Computed]
    public function completedOrdersSum(): float
    {
        return (float) Auth::user()
            ->orders()
            ->where('order_status_id', 11)
            ->sum('cost');
    }

    public function render(): View
    {
        return view('livewire.profile.discount.lists');
    }
}
