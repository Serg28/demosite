<div>
    @if($isOpen && $component)
        <div
            x-data
            x-on:keydown.escape.window="$wire.close()"
            class="relative z-50"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                class="fixed inset-0 bg-black/50 transition-opacity"
                wire:click="close"
                aria-hidden="true"
            ></div>

            {{-- Modal panel --}}
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                    class="relative w-full bg-white rounded-xl shadow-2xl {{ $maxWidth }}"
                    @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                    {{-- Close button --}}
                    <button
                        wire:click="close"
                        class="absolute top-4 right-4 z-10 text-ink-light hover:text-ink transition-colors"
                        aria-label="{{ __t('Закрити') }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Динамічний Livewire-компонент (lazy: в DOM тільки коли відкрито) --}}
                    @livewire($component, $arguments, key($component . '-' . md5(json_encode($arguments))))
                </div>
            </div>
        </div>
    @endif
</div>
