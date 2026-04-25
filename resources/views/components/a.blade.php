@props(['href','class', 'activeClass', 'wire'])
<a @if($currentUrl !== $href)
        href="{{ $href }}"
        class="{{$class ?? ''}} {{$activeClass ?? ''}}"
        {{$wire ? 'wire:navigate.hover' : ''}}
        {{ $attributes }}
   @else
        class="{{$class ?? ''}}"
   @endif
>
    {{ $slot }}
</a>
