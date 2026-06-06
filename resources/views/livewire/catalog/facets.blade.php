<div class="lw-catalog-facets space-y-3"
     wire:key="facets-{{ $this->category->id }}"
     data-base-path="{{ $this->basePath }}">

    {{-- Boolean filter toggles — driven by FilterUrlService::getBooleanFilterDefinitions() --}}
    @foreach($this->facets['boolean_filters'] ?? [] as $boolFilter)
        <div class="card p-4">
            <a href="{{ $boolFilter['toggle_url'] }}" class="js-filter-link flex items-center gap-3">
                <div class="relative w-10 h-5 rounded-full transition-colors {{ $boolFilter['is_active'] ? 'bg-brand' : 'bg-gray-200' }}">
                    <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform {{ $boolFilter['is_active'] ? 'translate-x-5' : 'translate-x-0.5' }}"></div>
                </div>
                <span class="text-sm font-medium select-none">{{ $boolFilter['label'] }}</span>
            </a>
        </div>
    @endforeach

    {{-- Price range --}}
    @if(($this->facets['price']['max'] ?? 0) > 0)
        @php
            $priceMin = $this->facets['price']['min'];
            $priceMax = $this->facets['price']['max'];
            $priceCurrentMin = $this->activeFilters['min_price'] ?? $priceMin;
            $priceCurrentMax = $this->activeFilters['max_price'] ?? $priceMax;
        @endphp
        <div class="card p-4" wire:key="facet-price">
            <h3 class="font-semibold text-sm mb-3">{{ __t('Ціна') }}, {{ setting('currency') }}</h3>
            <div class="js-range-slider"
                 data-char-slug="price"
                 data-base-path="{{ $this->basePath }}"
                 data-min="{{ $priceMin }}"
                 data-max="{{ $priceMax }}"
                 data-current-min="{{ $priceCurrentMin }}"
                 data-current-max="{{ $priceCurrentMax }}">
                <div class="values flex gap-2 items-center mb-3">
                    <input type="number" class="minValue field text-center text-sm px-2 py-2"
                           value="{{ $priceCurrentMin }}" min="{{ $priceMin }}" max="{{ $priceMax }}">
                    <span class="text-gray-400 flex-shrink-0">—</span>
                    <input type="number" class="maxValue field text-center text-sm px-2 py-2"
                           value="{{ $priceCurrentMax }}" min="{{ $priceMin }}" max="{{ $priceMax }}">
                </div>
                {{-- Single container: track + handles + draggable inputs all at h-8 --}}
                <div class="relative h-8 mx-1 my-1">
                    <div class="range-track absolute top-1/2 -translate-y-1/2 left-0 right-0 h-1.5 bg-gray-200 rounded-full pointer-events-none">
                        <div class="range absolute h-full bg-brand rounded-full"></div>
                    </div>
                    <div class="minHandle absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 bg-white border-2 border-brand rounded-full shadow pointer-events-none z-10"></div>
                    <div class="maxHandle absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 bg-white border-2 border-brand rounded-full shadow pointer-events-none z-10"></div>
                    <input type="range" class="minSlider absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                           min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $priceCurrentMin }}">
                    <input type="range" class="maxSlider absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
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
                $rangeData = $this->activeFilters['characteristics'][$facet['characteristic_id']] ?? null;
                $rCurMin = is_array($rangeData) && isset($rangeData['min']) ? $rangeData['min'] : $rMin;
                $rCurMax = is_array($rangeData) && isset($rangeData['max']) ? $rangeData['max'] : $rMax;
            @endphp
            <div class="card p-4" wire:key="facet-{{ $facet['characteristic_id'] }}">
                <h3 class="font-semibold text-sm mb-3">{{ $facet['characteristic_title'] }}</h3>
                <div class="js-range-slider"
                     data-char-slug="{{ $facet['characteristic_slug'] }}"
                     data-base-path="{{ $this->basePath }}"
                     data-min="{{ $rMin }}"
                     data-max="{{ $rMax }}"
                     data-current-min="{{ $rCurMin }}"
                     data-current-max="{{ $rCurMax }}">
                    <div class="values flex gap-2 items-center mb-3">
                        <input type="number" class="minValue field text-center text-sm px-2 py-2"
                               value="{{ $rCurMin }}" min="{{ $rMin }}" max="{{ $rMax }}">
                        <span class="text-gray-400 flex-shrink-0">—</span>
                        <input type="number" class="maxValue field text-center text-sm px-2 py-2"
                               value="{{ $rCurMax }}" min="{{ $rMin }}" max="{{ $rMax }}">
                    </div>
                    {{-- Single container: track + handles + draggable inputs all at h-8 --}}
                    <div class="relative h-8 mx-1 my-1">
                        <div class="range-track absolute top-1/2 -translate-y-1/2 left-0 right-0 h-1.5 bg-gray-200 rounded-full pointer-events-none">
                            <div class="range absolute h-full bg-brand rounded-full"></div>
                        </div>
                        <div class="minHandle absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 bg-white border-2 border-brand rounded-full shadow pointer-events-none z-10"></div>
                        <div class="maxHandle absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 bg-white border-2 border-brand rounded-full shadow pointer-events-none z-10"></div>
                        <input type="range" class="minSlider absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                               min="{{ $rMin }}" max="{{ $rMax }}" value="{{ $rCurMin }}">
                        <input type="range" class="maxSlider absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                               min="{{ $rMin }}" max="{{ $rMax }}" value="{{ $rCurMax }}">
                    </div>
                </div>
            </div>
        @else
            {{-- Checkbox characteristic --}}
            <div class="card p-4"
                 wire:key="facet-{{ $facet['characteristic_id'] }}"
                 x-data="filterGroup"
                 data-limit="8"
                 data-total="{{ count($facet['options']) }}"
                 data-label-show="{{ __t('Показати всі') }}"
                 data-label-collapse="{{ __t('Згорнути') }}">

                <h3 class="font-semibold text-sm mb-3">{{ $facet['characteristic_title'] }}</h3>

                {{-- Search input — hidden without JS via x-cloak --}}
                @if(count($facet['options']) > 8)
                    <div x-cloak class="mb-2">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="{{ __t('Пошук...') }}"
                            class="w-full px-2 py-1 text-sm border rounded focus:outline-none focus:ring-1 focus:ring-brand"
                        >
                    </div>
                @endif

                <div class="space-y-1.5">
                    @foreach($facet['options'] as $index => $option)
                        <label
                            wire:key="opt-{{ $option['id'] }}"
                            x-show="search ? $el.querySelector('a > span')?.textContent?.toLowerCase().includes(search.toLowerCase()) : (showAll || {{ $index }} < limit)"
                            class="js-filter-option flex items-center gap-2 py-0.5
                                {{ $option['is_disabled'] ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:text-brand' }}">
                            <input
                                type="checkbox"
                                id="opt-{{ $option['id'] }}"
                                data-char-slug="{{ $facet['characteristic_slug'] }}"
                                data-opt-slug="{{ $option['slug'] }}"
                                class="js-filter-input w-4 h-4 rounded border-gray-300 text-brand accent-brand flex-shrink-0"
                                @checked($option['is_active'])
                                @disabled($option['is_disabled'])>
                            <a href="#"
                               class="js-filter-link flex-1 flex items-center justify-between text-sm"
                               data-input-id="opt-{{ $option['id'] }}">
                                <span>{{ $option['title'] }}</span>
                                <span class="text-xs text-ink-light ml-1">({{ $option['count'] }})</span>
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
                        class="mt-2 text-sm text-brand hover:underline">
                    </button>
                @endif
            </div>
        @endif
    @endforeach

    {{-- Скинути всі фільтри --}}
    <a href="{{ rtrim($this->basePath, '/') . '/' }}"
       class="btn btn-o btn-sm w-full js-filter-link">
        {{ __t('Скинути фільтри') }}
    </a>
</div>
