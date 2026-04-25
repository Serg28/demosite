<div class="policy-row" itemscope itemtype="http://schema.org/SiteNavigationElement">
    @foreach ($footerMenu as $menuItem)
        @php
            $menuItemUrl = getUrl($menuItem->getUrl());
        @endphp
        @if ($currentUrl !== $menuItemUrl)
            <a wire:navigate class="policy" href="{{ $menuItemUrl }}" wire:key="{{ $menuItem->id }}" itemprop="url" @active('{{ $menuItemUrl }}')>
            {{ $menuItem->t('title') }}
            </a>
        @else
            <a class="policy" wire:key="{{ $menuItem->id }}">{{ $menuItem->t('title') }}</a>
        @endif
    @endforeach
</div>