@props(['items' => []])

@if(count($items) > 1)
<nav aria-label="{{ __t('Хлібні крихти') }}" class="py-3 text-sm text-gray-500">
    <ol class="flex flex-wrap items-center gap-1" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $key => $item)
            <li class="flex items-center gap-1"
                itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if($loop->last)
                    <span class="text-ink font-medium truncate max-w-xs" itemprop="name">{{ $item->title }}</span>
                @else
                    <a href="{{ $item->url }}"
                       class="hover:text-brand transition-colors truncate max-w-xs"
                       itemprop="item">
                        <span itemprop="name">{{ $item->title }}</span>
                    </a>
                    <svg class="h-3 w-3 flex-shrink-0 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
                <meta itemprop="position" content="{{ $key + 1 }}"/>
            </li>
        @endforeach
    </ol>
</nav>
@endif
