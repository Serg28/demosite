<div class="flex items-center gap-2">
    {{-- Сортування: select --}}
    <select data-js-sort-select
            class="field text-sm"
            style="padding:8px 12px;width:auto">
        @foreach($this->sortOptions as $option)
            <option value="{{ $option['url_key'] ?? '' }}"
                    {{ $option['is_active'] ? 'selected' : '' }}>
                {{ __t($option['label']) }}
            </option>
        @endforeach
    </select>

    {{-- Перемикач виду: grid / list --}}
    <div x-data class="flex gap-1">
        <button type="button"
                @click="$store.catalog.setView('grid')"
                :class="$store.catalog.view === 'grid' ? 'bg-brand text-white' : 'bg-white text-gray-400'"
                class="w-9 h-9 border border-gray-200 rounded-lg flex items-center justify-center transition-colors"
                title="{{ __t('Плитка') }}">⊞</button>
        <button type="button"
                @click="$store.catalog.setView('list')"
                :class="$store.catalog.view === 'list' ? 'bg-brand text-white' : 'bg-white text-gray-400'"
                class="w-9 h-9 border border-gray-200 rounded-lg flex items-center justify-center transition-colors"
                title="{{ __t('Список') }}">☰</button>
    </div>
</div>
