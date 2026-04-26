<div class="facets-panel space-y-6">
    <!-- Price Range Facet -->
    @if(isset($this->facets['price']) && $this->facets['price']['max'] > 0)
        <div class="facet-group border-b pb-6">
            <h3 class="font-semibold mb-4">{{ __t('Ціна') }}</h3>
            <div class="flex gap-2 items-center">
                <input
                    type="number"
                    wire:model.lazy="currentFilters.min_price"
                    placeholder="{{ __t('Від') }}"
                    min="{{ $this->facets['price']['min'] }}"
                    max="{{ $this->facets['price']['max'] }}"
                    class="w-24 px-2 py-1 border rounded text-sm"
                >
                <span class="text-gray-400">—</span>
                <input
                    type="number"
                    wire:model.lazy="currentFilters.max_price"
                    placeholder="{{ __t('До') }}"
                    min="{{ $this->facets['price']['min'] }}"
                    max="{{ $this->facets['price']['max'] }}"
                    class="w-24 px-2 py-1 border rounded text-sm"
                >
            </div>
        </div>
    @endif

    <!-- Characteristic Facets -->
    @foreach($this->facets['characteristics'] ?? [] as $facet)
        <div class="facet-group border-b pb-6" wire:key="facet-{{ $facet['characteristic_id'] }}">
            <h3 class="font-semibold mb-4">{{ $facet['characteristic_title'] }}</h3>

            <div class="space-y-2">
                @foreach($facet['options'] as $option)
                    @php
                        $isChecked = in_array($option['id'], $this->currentFilters['characteristics'][$facet['characteristic_id']] ?? []);
                    @endphp
                    <label class="flex items-center gap-2 cursor-pointer" wire:key="option-{{ $option['id'] }}">
                        <input
                            type="checkbox"
                            wire:click="toggleOption({{ $facet['characteristic_id'] }}, {{ $option['id'] }})"
                            @checked($isChecked)
                            class="rounded border-gray-300"
                        >
                        <span class="text-sm">{{ $option['title'] }}</span>
                        <span class="text-xs text-gray-400">({{ $option['count'] }})</span>
                    </label>
                @endforeach

                @if($facet['has_more'] ?? false)
                    <button
                        wire:click="toggleFacet({{ $facet['characteristic_id'] }})"
                        class="text-sm text-blue-600 mt-2 hover:underline"
                    >
                        {{ __t('Показати ще') }}
                    </button>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Clear filters -->
    @if(!empty(array_filter($this->currentFilters['characteristics'] ?? [])) || $this->currentFilters['min_price'] || $this->currentFilters['max_price'])
        <button wire:click="clearFilters" class="text-sm text-red-500 hover:underline">
            {{ __t('Скинути фільтри') }}
        </button>
    @endif
</div>
