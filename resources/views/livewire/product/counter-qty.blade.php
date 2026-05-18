<div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
    <button
        wire:click="decrement"
        class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition disabled:opacity-40"
        :disabled="{{ $quantity <= $min ? 'true' : 'false' }}"
        aria-label="{{ __t('Зменшити') }}"
    >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
        </svg>
    </button>

    <input
        wire:model.lazy="quantity"
        type="number"
        min="{{ $min }}"
        max="{{ $max }}"
        class="w-14 text-center text-sm font-medium border-0 focus:ring-0 focus:outline-none py-2"
        aria-label="{{ __t('Кількість') }}"
    />

    <button
        wire:click="increment"
        class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition disabled:opacity-40"
        :disabled="{{ $quantity >= $max ? 'true' : 'false' }}"
        aria-label="{{ __t('Збільшити') }}"
    >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </button>
</div>
