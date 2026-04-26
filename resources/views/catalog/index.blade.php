<x-layouts.shop>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold mb-6">
            {{ is_array($category->title) ? ($category->title['ua'] ?? $category->title['ru'] ?? '') : $category->title }}
        </h1>

        <div class="flex gap-8">
            <!-- Sidebar: Facets -->
            <aside class="w-64 flex-shrink-0">
                <livewire:catalog.facets :category="$category" />
            </aside>

            <!-- Main: Sort + Products -->
            <main class="flex-1 min-w-0">
                <livewire:catalog.sort-bar />
                <livewire:catalog.product-list :category="$category" />
            </main>
        </div>
    </div>
</x-layouts.shop>
