<div class="lang-menu flex v--center fsz-14 relative">
    <ul class="lang-menu flex v--center fsz-14 relative">
        @foreach ($languages as $language)
            @php
                $menuItemUrl = isset($this->page) ? $this->page->getFullUrl($language) : $currentUrl;
            @endphp
            <li wire:key="lang-menu-{{$language}}" @active('{{ $currentUrl }}') @if($currentLang===$language) class="current" @endif >
                <a wire:navigate href="{{geturl($menuItemUrl, $language)}}" {{$language === $currentLang ? 'class="current"' : ''}}>
                    {{mb_strtoupper($language)}}
                </a>
            </li>
        @endforeach
    </ul>
</div>