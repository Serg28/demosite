<div class="facets-panel space-y-6">
    @foreach($this->facets as $facet)
        <div class="facet-group border-b pb-6">
            <h3 class="font-semibold mb-4">{{ $facet['characteristic_title'] ?? $facet['facet_title'] }}</h3>

            <div class="space-y-2">
                @foreach($facet['options'] as $option)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="currentFilters.{{ $facet['characteristic_id'] ?? 'brands' }}"
                            value="{{ $option['id'] }}"
                            class="rounded"
                        >
                        <span class="text-sm">{{ $option['title'] }}</span>
                        <span class="text-xs text-gray-500">({{ $option['count'] }})</span>
                    </label>
                @endforeach

                @if($facet['has_more'] ?? false)
                    <button
                        wire:click="toggleFacet({{ $facet['characteristic_id'] }})"
                        class="text-sm text-blue-600 mt-2"
                    >
                        Показати ще
                    </button>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Price Range -->
    <div class="facet-group border-b pb-6">
        <h3 class="font-semibold mb-4">Ціна</h3>
        <div class="flex gap-2">
            <input
                type="number"
                wire:model="currentFilters.min_price"
                placeholder="Від"
                class="w-20 px-2 py-1 border rounded text-sm"
            >
            <input
                type="number"
                wire:model="currentFilters.max_price"
                placeholder="До"
                class="w-20 px-2 py-1 border rounded text-sm"
            >
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:updated', () => {
        Livewire.dispatch('filter-changed');
    });
</script>
