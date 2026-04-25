<!DOCTYPE html>
<html lang="{{\App::getLocale()}}" @yield('microdata-page')>
<head>
    @include('layouts.partials.head')
    @include('layouts.partials.internet_marketing_head')
    @stack('header')
</head>
<body id="top" class="disable-scrollbar">
@include('layouts.partials.internet_marketing_body')

@include('partials.header.header_checkout')

@yield('main')

@include('partials.footer.footer_checkout')
{{--@include('partials.popups')--}}


<script>
    window.lang = '{{App::getLocale() == 'ua' ? '/' : '/' . App::getLocale() . '/' }}';
    window.public_key = "{{ config('recaptcha.api_site_key') }}";
    /*
    window.addEventListener('livewire:navigated', () => {
        window.scrollTo({ top: 0 });
    });*/
</script>
<script src="/js/translation_{{App::getLocale()}}.js"></script>
<script src="/assets/js/validation.js" data-navigate-once></script>
<script src="/assets/js/fine-min.js" data-navigate-once></script>
<script src="/assets/js/zepto.min.js" data-navigate-once></script>

@livewireStyles
@livewireScripts

@notLighthouse
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.2.5/simplebar.min.js"></script>
<script src="{{ mix('/assets/js/scripts.min.js')}}"></script>
@livewire('wire-elements-modal')
<notifications-popup styles-path="{{ mix('/assets/css/notification.css') }}"></notifications-popup>
@endnotLighthouse

{!! setting('body') !!}

@yield('scripts')
@stack('footer-styles')
@stack('footer-scripts')

</body>

</html>


