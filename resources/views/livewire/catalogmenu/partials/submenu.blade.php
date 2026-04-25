<ul class="{{$topClass}}">
    @foreach($items as $children)
        <li class="{!! $children->css_class ?? '' !!}" wire:key="{{$wire_key_prefix}}-{{$loop->index}}-{{$children->id}}">

            <a wire:navigate.hover href="{{getUrl($children->getUrl())}}" {!! $children->is_target_blank ? 'target="_blank"' : '' !!} itemprop="url">{!! $children->t('title') !!}</a>

            @if($children->children->isNotEmpty())
                @include('livewire.catalogmenu.partials.submenu', [
                    'items' => $children->children,
                    'topClass' => 'sub-menu',
                    'wire_key_prefix' => 'catmenuTop-header-right-submenu-level-3'
                ])
            @endif
        </li>
    @endforeach
</ul>
