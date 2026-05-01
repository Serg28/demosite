{{--
    Toast-сповіщення (Alpine.js).

    Підключається один раз у shop.blade.php:
        <x-toast />

    Виклик з PHP (Livewire з трейтом HasNotifications):
        $this->notify('Товар додано', 'Успіх', 'success')

    Виклик з JS:
        window.notify('error', 'Помилка завантаження')
        Livewire.dispatch('notify', { type: 'info', message: 'Оновлено' })
--}}
<div
    x-data="toast"
    x-on:notify.window="add($event.detail.type, $event.detail.message, $event.detail.title ?? '')"
    class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 w-80"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="item in items" :key="item.id">
        <div
            x-show="item.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="colorClass(item.type)"
            class="flex items-start gap-3 p-4 rounded-xl border shadow-lg"
        >
            {{-- Icon --}}
            <svg
                :class="iconColor(item.type)"
                class="w-5 h-5 mt-0.5 flex-shrink-0"
                fill="none" viewBox="0 0 24 24" stroke="currentColor"
            >
                <g x-html="iconSvg(item.type)"></g>
            </svg>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <p x-show="item.title" x-text="item.title" class="font-semibold text-sm leading-tight mb-0.5"></p>
                <p x-text="item.message" class="text-sm leading-tight"></p>
            </div>

            {{-- Close --}}
            <button
                @click="remove(item.id)"
                class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                :aria-label="'{{ __t('Закрити') }}'"
            >
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </template>
</div>
