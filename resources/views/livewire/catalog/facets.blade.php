<div class="lw-catalog-facets" wire:key="facets-{{ $this->category->id }}">

    {{-- Active filter tags --}}
    @if(!empty($this->activeFilterTags))
        <div class="active-filters flex flex-wrap gap-2 mb-4">
            @foreach($this->activeFilterTags as $tag)
                <a href="{{ $tag['remove_url'] }}"
                   class="js-filter-link inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200">
                    {{ $tag['char_title'] }}: {{ $tag['opt_title'] }}
                    <span aria-hidden="true">&times;</span>
                </a>
            @endforeach
            <a href="{{ $this->facets['clear_url'] }}"
               class="js-filter-link text-xs text-red-500 hover:underline self-center">
                {{ __t('Скинути все') }}
            </a>
        </div>
    @endif

    {{-- Price range --}}
    @if(($this->facets['price']['max'] ?? 0) > 0)
        @php
            $priceMin = $this->facets['price']['min'];
            $priceMax = $this->facets['price']['max'];
            $priceCurrentMin = $this->facets['price']['current_min'] ?? $priceMin;
            $priceCurrentMax = $this->facets['price']['current_max'] ?? $priceMax;
        @endphp
        <div class="facet-group mb-6 pb-6 border-b" wire:key="facet-price">
            <h3 class="font-semibold mb-3">{{ __t('Ціна') }}</h3>
            <div class="js-range-slider"
                 data-char-slug="price"
                 data-base-path="{{ $this->basePath }}"
                 data-filters-path="{{ $this->filtersPath }}"
                 data-min="{{ $priceMin }}"
                 data-max="{{ $priceMax }}"
                 data-current-min="{{ $priceCurrentMin }}"
                 data-current-max="{{ $priceCurrentMax }}">
                <div class="values flex gap-2 items-center mb-3">
                    <input type="number" class="minValue w-24 px-2 py-1 border rounded text-sm"
                           value="{{ $priceCurrentMin }}" min="{{ $priceMin }}" max="{{ $priceMax }}">
                    <span class="text-gray-400">—</span>
                    <input type="number" class="maxValue w-24 px-2 py-1 border rounded text-sm"
                           value="{{ $priceCurrentMax }}" min="{{ $priceMin }}" max="{{ $priceMax }}">
                </div>
                <div class="range-track relative h-1 bg-gray-200 rounded mx-2">
                    <div class="range absolute h-full bg-blue-500 rounded"></div>
                </div>
                <div class="relative mt-1">
                    <input type="range" class="minSlider absolute w-full h-1 opacity-0 cursor-pointer"
                           min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $priceCurrentMin }}">
                    <input type="range" class="maxSlider absolute w-full h-1 opacity-0 cursor-pointer"
                           min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $priceCurrentMax }}">
                </div>
            </div>
        </div>
    @endif

    {{-- Characteristic facets --}}
    @foreach($this->facets['characteristics'] ?? [] as $facet)
        @if($facet['is_range_type'])
            {{-- Range characteristic --}}
            @php
                $rMin = $facet['range_min'] ?? 0;
                $rMax = $facet['range_max'] ?? 100;
                $rCurMin = $facet['range_current_min'] ?? $rMin;
                $rCurMax = $facet['range_current_max'] ?? $rMax;
            @endphp
            <div class="facet-group mb-6 pb-6 border-b" wire:key="facet-{{ $facet['characteristic_id'] }}">
                <h3 class="font-semibold mb-3">{{ $facet['characteristic_title'] }}</h3>
                <div class="js-range-slider"
                     data-char-slug="{{ $facet['characteristic_slug'] }}"
                     data-base-path="{{ $facet['range_url_base'] }}"
                     data-filters-path="{{ $facet['range_filters_path'] }}"
                     data-min="{{ $rMin }}"
                     data-max="{{ $rMax }}"
                     data-current-min="{{ $rCurMin }}"
                     data-current-max="{{ $rCurMax }}">
                    <div class="values flex gap-2 items-center mb-3">
                        <input type="number" class="minValue w-24 px-2 py-1 border rounded text-sm"
                               value="{{ $rCurMin }}" min="{{ $rMin }}" max="{{ $rMax }}">
                        <span class="text-gray-400">—</span>
                        <input type="number" class="maxValue w-24 px-2 py-1 border rounded text-sm"
                               value="{{ $rCurMax }}" min="{{ $rMin }}" max="{{ $rMax }}">
                    </div>
                    <div class="range-track relative h-1 bg-gray-200 rounded mx-2">
                        <div class="range absolute h-full bg-blue-500 rounded"></div>
                    </div>
                    <div class="relative mt-1">
                        <input type="range" class="minSlider absolute w-full h-1 opacity-0 cursor-pointer"
                               min="{{ $rMin }}" max="{{ $rMax }}" value="{{ $rCurMin }}">
                        <input type="range" class="maxSlider absolute w-full h-1 opacity-0 cursor-pointer"
                               min="{{ $rMin }}" max="{{ $rMax }}" value="{{ $rCurMax }}">
                    </div>
                </div>
            </div>
        @else
            {{-- Checkbox characteristic --}}
            <div class="facet-group mb-6 pb-6 border-b"
                 wire:key="facet-{{ $facet['characteristic_id'] }}"
                 x-data="filterGroup"
                 data-limit="8"
                 data-total="{{ count($facet['options']) }}"
                 data-label-show="{{ __t('Показати всі') }}"
                 data-label-collapse="{{ __t('Згорнути') }}">

                <h3 class="font-semibold mb-3">{{ $facet['characteristic_title'] }}</h3>

                {{-- Search input — hidden without JS via x-cloak --}}
                @if(count($facet['options']) > 8)
                    <div x-cloak class="mb-2">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="{{ __t('Пошук...') }}"
                            class="w-full px-2 py-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-blue-400"
                        >
                    </div>
                @endif

                <div class="space-y-1">
                    @foreach($facet['options'] as $index => $option)
                        <label
                            wire:key="opt-{{ $option['id'] }}"
                            x-show="isVisible({{ $index }}, {{ json_encode($option['title']) }})"
                            class="js-filter-option flex items-center gap-2 py-0.5
                                {{ $option['is_disabled'] ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer group hover:text-blue-600' }}">
                            <input
                                type="checkbox"
                                id="opt-{{ $option['id'] }}"
                                data-url="{{ $option['toggle_url'] }}"
                                class="js-filter-input w-4 h-4 rounded border-gray-300 text-blue-600 flex-shrink-0"
                                @checked($option['is_active'])
                                @disabled($option['is_disabled'])>
                            <a href="{{ $option['seo_url'] }}"
                               class="js-filter-link flex-1 flex items-center justify-between text-sm"
                               data-input-id="opt-{{ $option['id'] }}">
                                <span>{{ $option['title'] }}</span>
                                <span class="text-xs text-gray-400 ml-1">({{ $option['count'] }})</span>
                            </a>
                        </label>
                    @endforeach
                </div>

                {{-- Toggle button — hidden without JS --}}
                @if(count($facet['options']) > 8)
                    <button
                        x-cloak
                        x-show="total > limit"
                        @click="showAll = !showAll"
                        x-text="toggleLabel"
                        class="mt-2 text-sm text-blue-600 hover:underline">
                    </button>
                @endif
            </div>
        @endif
    @endforeach
</div>
