<div class="sort-bar flex items-center gap-4 mb-8 pb-4 border-b">
    <span class="text-sm font-medium">{{ __t('Сортування') }}:</span>

    @foreach($this->sortOptions as $option)
        <a href="#"
           data-js-sort="{{ $option['url_key'] ?? '' }}"
           class="text-sm px-3 py-1.5 rounded-lg border transition-colors
               {{ $option['is_active']
                   ? 'bg-blue-600 text-white border-blue-600'
                   : 'border-gray-300 text-gray-600 hover:border-blue-400 hover:text-blue-600' }}">
            {{ __t($option['label']) }}
        </a>
    @endforeach
</div>
