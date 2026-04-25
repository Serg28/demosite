<div class="sort-bar flex items-center justify-between mb-8 pb-4 border-b">
    <div class="flex items-center gap-4">
        <label class="text-sm font-medium">Сортування:</label>

        <select
            wire:change="updateSort($event.target.value, '{{ $sortDir }}')"
            class="px-3 py-2 border rounded-lg text-sm"
        >
            <option value="priority" {{ $sortBy === 'priority' ? 'selected' : '' }}>
                По популярності
            </option>
            <option value="price" {{ $sortBy === 'price' ? 'selected' : '' }}>
                По ціні
            </option>
            <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>
                По назві
            </option>
            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>
                Новинки
            </option>
        </select>

        <button
            wire:click="toggleDirection()"
            class="px-3 py-2 border rounded-lg text-sm font-medium hover:bg-gray-100"
        >
            {{ $sortDir === 'asc' ? '↑ Зростаючо' : '↓ Спадаючо' }}
        </button>
    </div>

    <div class="text-sm text-gray-600">
        <span wire:loading wire:target="updateSort" class="text-blue-600">
            Завантаження...
        </span>
    </div>
</div>
