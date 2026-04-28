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

            <div class="space-y-1">
                @foreach($facet['options'] as $option)
                    @php
                        $isChecked = in_array($option['id'], $this->currentFilters['characteristics'][$facet['characteristic_id']] ?? []);
                        $optionUrl = '?' . $facet['characteristic_slug'] . '=' . $option['slug'];
                    @endphp
                    <div wire:key="option-{{ $option['id'] }}">
                        <a href="{{ $optionUrl }}"
                           wire:click.prevent="toggleOption({{ $facet['characteristic_id'] }}, {{ $option['id'] }})"
                           class="flex items-center gap-2 py-1 w-full cursor-pointer group hover:text-blue-600 transition-colors">
                            <span class="w-4 h-4 rounded border flex-shrink-0 flex items-center justify-center transition-colors
                                {{ $isChecked ? 'bg-blue-600 border-blue-600' : 'border-gray-300 group-hover:border-blue-400' }}">
                                @if($isChecked)
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="text-sm">{{ $option['title'] }}</span>
                            <span class="text-xs text-gray-400 ml-auto">({{ $option['count'] }})</span>
                        </a>
                    </div>
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
