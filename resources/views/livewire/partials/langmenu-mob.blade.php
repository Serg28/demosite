<div class="mob-menu-top-row flex v--center h--between">
    <ul class="lang-menu">
        <li class="menu-item-has-children"><span class="text">{{mb_strtoupper($currentLang)}}</span>
            <ul class="sub-menu">
                @foreach ($languages as $language)
                    @php
                        $menuItemUrl = isset($this->page) ? $this->page->getFullUrl($language) : $currentUrl;
                    @endphp
                    @if($currentLang!==$language)
                        <li wire:key="lang-mob-menu-{{$language}}" @active('{{ $currentUrl }}')>
                        <a wire:navigate href="{{geturl($menuItemUrl, $language)}}" {{$language == $currentLang ? 'class="active"' : ''}}>
                            {{mb_strtoupper($language)}}
                        </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
    </ul>
    <div class="closer"><img src="/assets/images/closer.svg" alt=""></div>
</div>
