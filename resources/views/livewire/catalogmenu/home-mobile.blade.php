<div class="hidden-row">
    @if($menu->isNotEmpty())
        <ul itemscope itemtype="http://schema.org/SiteNavigationElement">
            @foreach($menu as $item)
                <li wire:key="catalog-menu-fs-{{$item->id}}">
                    <a href="{{getUrl($item->getUrl())}}" wire:navigate.hover class="flex v--center {!! $item->css_class ?? '' !!}" itemprop="url">
                        <x-catalogmenu.item-icon :picture="$item->picture" :alt="$item->t('title')" />
                        <span class="text">{!! $item->t('title') !!}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>