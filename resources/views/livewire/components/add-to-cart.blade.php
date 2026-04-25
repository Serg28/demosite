<div class="flex flex-col gap-3">
    <!-- Quantity Selector -->
    <div class="flex items-center gap-2 border border-gray-300 rounded-lg w-fit">
        <button
            wire:click="decrementQuantity"
            class="px-3 py-2 hover:bg-gray-100 transition-colors"
        >
            −
        </button>
        <span class="px-4 py-2 text-center w-12">{{ $quantity }}</span>
        <button
            wire:click="incrementQuantity"
            class="px-3 py-2 hover:bg-gray-100 transition-colors"
        >
            +
        </button>
    </div>

    <!-- Add to Cart Button -->
    <button
        wire:click="addToCart"
        wire:loading.attr="disabled"
        class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
    >
        <span wire:loading.remove>
            🛒 {{ __t('Добавить в корзину') }}
        </span>
        <span wire:loading>
            <livewire:components.loading-spinner size="sm" color="white" />
        </span>
    </button>

    <!-- Success Message -->
    @if($isAdded)
        <div class="text-sm text-green-600 font-medium">
            ✓ {{ __t('Добавлено в корзину') }}
        </div>
    @endif
</div>
