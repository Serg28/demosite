<div class="catalog-side-bar {{$class ?? ''}}">
    <p class="fsz-18 fw-600">{{ __t('Популярні категорії') }}</p>
    <ul class="flex fd--column">
        @if(!empty($topMenu))
            @foreach ($topMenu as $item)
                @php
                    $menuItemUrl = getUrl($item->getUrl());
                @endphp
                <li wire:key="sidebar-menu-{{ $item->id }}" @active('{{ $menuItemUrl }}')>
                <a wire:navigate href="{{ $menuItemUrl }}" itemprop="url">
                    {{ $item->t('title') }}
                </a>
                </li>
            @endforeach
        @endif
    </ul>
</div>

