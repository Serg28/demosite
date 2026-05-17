@props([
    'label'         => '',
    'placeholder'   => '',
    'searchUrl'     => '',
    'selectedId'    => null,
    'selectedTitle' => '',
    'selectMethod'  => '',
    'clearMethod'   => '',
    'minChars'      => 2,
])

<div
    x-data="{
        open: false,
        query: @js($selectedTitle),
        isSelected: @js($selectedId !== null),
        results: [],
        loading: false,
        selectMethod: @js($selectMethod),
        clearMethod: @js($clearMethod),
        searchUrl: @js($searchUrl),
        minChars: {{ (int) $minChars }},
        async search() {
            if (this.query.length < this.minChars) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            try {
                const urlObj = new URL(this.searchUrl, window.location.origin);
                urlObj.searchParams.set('q', this.query);
                const resp = await fetch(urlObj.toString());
                this.results = await resp.json();
                this.open = this.results.length > 0;
            } catch {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },
        select(id, title) {
            this.query = title;
            this.isSelected = true;
            this.results = [];
            this.open = false;
            $wire[this.selectMethod](id, title);
        },
        clear() {
            this.query = '';
            this.isSelected = false;
            this.results = [];
            this.open = false;
            $wire[this.clearMethod]();
        },
    }"
    @click.outside="open = false"
    class="relative"
>
    @if($label)
        <label class="text-sm font-medium mb-1.5 block">{{ $label }}</label>
    @endif
    <div class="relative">
        <input
            x-model="query"
            @input.debounce.300ms="search"
            type="text"
            class="field pr-8"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
        >
        <button
            x-show="isSelected"
            x-cloak
            @click.prevent="clear"
            type="button"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            aria-label="{{ __t('Очистити') }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto"
    >
        <template x-for="item in results" :key="item.id">
            <button
                @click.prevent="select(item.id, item.title)"
                type="button"
                class="w-full text-left px-4 py-2.5 text-sm hover:bg-brand-light transition-colors"
                x-text="item.title"
            ></button>
        </template>
    </div>

    <p
        x-show="!open && !loading && !isSelected && query.length >= minChars && results.length === 0"
        x-cloak
        class="text-xs text-ink-muted mt-1"
    >{{ __t('Нічого не знайдено') }}</p>
</div>
