<div class="flex flex-col gap-4">
    @if(!empty($images))
        <!-- Main Image -->
        <div class="relative bg-gray-100 rounded-lg overflow-hidden aspect-square">
            @if(isset($images[$selectedIndex]))
                <img
                    src="{{ $images[$selectedIndex] }}"
                    alt="Product image {{ $selectedIndex + 1 }}"
                    class="w-full h-full object-cover"
                />
            @endif

            <!-- Navigation Arrows -->
            @if(count($images) > 1)
                <button
                    wire:click="previousImage"
                    class="absolute left-3 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 transition-all"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <button
                    wire:click="nextImage"
                    class="absolute right-3 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 transition-all"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @endif
        </div>

        <!-- Thumbnails -->
        @if(count($images) > 1)
            <div class="flex gap-2 overflow-x-auto">
                @foreach($images as $index => $image)
                    <button
                        wire:click="selectImage({{ $index }})"
                        class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-colors {{ $selectedIndex === $index ? 'border-blue-600' : 'border-gray-200' }}"
                    >
                        <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    @else
        <div class="bg-gray-200 rounded-lg aspect-square flex items-center justify-center">
            <p class="text-gray-600">No images available</p>
        </div>
    @endif
</div>
