<div class="flex items-center gap-2">
    @if($discountedPrice)
        <span class="line-through text-gray-500 text-sm">
            {{ $this->getFormattedPrice() }}
        </span>
        <span class="text-lg font-bold text-green-600">
            {{ number_format($discountedPrice, 2, ',', ' ') }} {{ $currency }}
        </span>
        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
            -{{ $this->getDiscount() }}%
        </span>
    @else
        <span class="text-lg font-bold text-gray-900">
            {{ $this->getFormattedPrice() }}
        </span>
    @endif
</div>
