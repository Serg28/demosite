<div class="center d--flex ai--center">
    <ul class="nav-menu flex v--center fsz-14">
    @if(!empty($topMenu))
        @loop ($topMenu as $item)
            @php
                $menuItemUrl = getUrl($item->getUrl());
            @endphp
            @if ($currentUrl !== $menuItemUrl)
                <li wire:key="nav-menu-{{ $item->id }}" @active('{{ $menuItemUrl }}')>
                <a wire:navigate href="{{ $menuItemUrl }}" itemprop="url">
                    {{ $item->t('title') }}
                </a>
                </li>
            @endif
            @if ($currentUrl == $menuItemUrl)
                <li wire:key="nav-menu-{{ $item->id }}" class="current">
                    <span>{{ $item->t('title') }}</span>
                </li>
            @endif
        @endloop
    @endif
    </ul>
</div>
