<x-layouts.shop
    :title="$category->getSeoTitle() . ' — ' . config('app.name')"
    :description="$category->getSeoDescription()"
    seoPartial="partials.seo_catalog"
    :page="$category"
    :count="$count ?? 0"
>
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-6">
        <x-breadcrumbs :items="$breadcrumbs ?? []" />

        <div class="flex gap-6 mt-4">
            {{-- Sidebar: Фільтри (w-64 як у шаблоні) --}}
            <aside class="w-64 flex-shrink-0 hidden md:block">
                <livewire:catalog.facets
                    :category="$category"
                    :basePath="$basePath"
                    :initialFiltersPath="$filters"
                    :initialFilters="$initialFilters"
                />
            </aside>

            {{-- Main: Сортування + Товари --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between mb-5 gap-4 flex-wrap">
                    <div x-data="{ count: {{ $count ?? 0 }} }"
                         @catalog-count-updated.window="count = $event.detail.count">
                        <h1 class="text-2xl font-bold">{{ $category->getSeoH1() }}</h1>
                        <p class="text-sm text-ink-muted mt-1">
                            <span x-text="count">{{ $count ?? 0 }}</span> {{ __t('товарів') }}
                        </p>
                    </div>
                    <livewire:catalog.sort-bar :sortBy="$sortBy" :sortDir="$sortDir" />
                </div>

                {{-- Active filter chips (оновлюються через Alpine після Livewire Facets render) --}}
                <div x-data="{ filterTags: [], resetUrl: '' }"
                     @active-filters-updated.window="filterTags = $event.detail.tags; resetUrl = $event.detail.resetUrl">
                    <div x-show="filterTags.length > 0"
                         x-cloak
                         class="flex flex-wrap gap-2 mb-4">
                        <template x-for="tag in filterTags" :key="tag.remove_url">
                            <a :href="tag.remove_url"
                               class="chip text-sm gap-2 js-filter-link no-underline cursor-pointer">
                                <span x-text="tag.char_title ? tag.char_title + ': ' + tag.opt_title : tag.opt_title"></span>
                                <span class="opacity-60 hover:text-red-500">✕</span>
                            </a>
                        </template>
                    </div>
                </div>

                <livewire:catalog.product-list
                    :category="$category"
                    :initialFilters="$initialFilters"
                    :sortBy="$sortBy"
                    :sortDir="$sortDir"
                    :initialPage="$initialPage"
                />
            </div>
        </div>
    </div>
</x-layouts.shop>
