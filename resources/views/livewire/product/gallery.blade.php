<div>
    {{-- Основне зображення / відео --}}
    <div class="w-full aspect-square rounded-xl mb-3 overflow-hidden bg-gray-50">
        @if($showVideo && $youtubeId)
            <iframe
                src="https://www.youtube.com/embed/{{ $youtubeId }}"
                class="w-full h-full"
                frameborder="0"
                allowfullscreen
            ></iframe>
        @elseif(! empty($images))
            <img
                src="{{ $images[$selectedIndex] }}"
                alt="{{ __t('Фото товару') }} {{ $selectedIndex + 1 }}"
                class="w-full h-full object-contain"
            />
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- Мініатюри --}}
    @if(count($images) > 1 || $youtubeId)
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach($images as $index => $image)
                <button
                    wire:click="selectImage({{ $index }})"
                    wire:key="thumb-{{ $index }}"
                    class="relative w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 border-2 transition-colors
                        {{ (! $showVideo && $selectedIndex === $index) ? 'border-brand' : 'border-transparent hover:border-gray-300' }}"
                >
                    <img src="{{ $image }}" alt="{{ __t('Фото') }} {{ $index + 1 }}" class="w-full h-full object-cover">
                </button>
            @endforeach

            @if($youtubeId)
                <button
                    wire:click="selectVideo"
                    class="relative w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 border-2 transition-colors
                        {{ $showVideo ? 'border-brand' : 'border-transparent hover:border-gray-300' }}
                        bg-gray-900 flex items-center justify-center"
                >
                    <span class="text-white text-lg">▶</span>
                </button>
            @endif
        </div>
    @endif
</div>
