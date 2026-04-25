<div class="info-page-side-bar p-12">
    <ul>
        @if(!empty($topMenu))
            @foreach ($topMenu as $item)
                @php
                    $menuItemUrl = getUrl($item->getUrl());
                @endphp
                @if ($currentUrl !== $menuItemUrl)
                    <li wire:key="sidebar-menu-{{ $item->id }}" @active('{{ $menuItemUrl }}')>
                    <a wire:navigate.hover href="{{ $menuItemUrl }}" itemprop="url" class="flex p-12">
                        {{ $item->t('title') }}
                    </a>
                    </li>
                @endif
                @if ($currentUrl == $menuItemUrl)
                    <li wire:key="sidebar-menu-{{ $item->id }}">
                        <span class="flex p-12 current">{{ $item->t('title') }}</span>
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
</div>

