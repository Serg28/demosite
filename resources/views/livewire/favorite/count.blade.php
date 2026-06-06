<span wire:ignore.self>
    @if($this->count > 0)
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center leading-none">
            {{ $this->count > 9 ? '9+' : $this->count }}
        </span>
    @endif
</span>
