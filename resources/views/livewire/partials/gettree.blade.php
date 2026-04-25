<div>
    <div class="tabs-row" itemscope itemtype="http://schema.org/SiteNavigationElement">
        @foreach ($tree as $menuItem)
            @php
                $menuItemUrl = getUrl($menuItem->getUrl());
            @endphp
                @if ($currentUrl !== $menuItemUrl)
                    <a wire:key="{{ $menuItem->id }}" wire:navigate href="{{ $menuItemUrl }}" itemprop="url" class="tab" @active('{{ $menuItemUrl }}')>
                    {{ $menuItem->t('title') }}
                    </a>
                @else
                    <a wire:key="{{ $menuItem->id }}" class="tab active">{{ $menuItem->t('title') }}</a>
                @endif
        @endforeach
    </div>
</div>