<!DOCTYPE html>
<html lang="{{\App::getLocale()}}" @yield('microdata-page')>
<head>
    @include('layouts.partials.head')
    @include('layouts.partials.internet_marketing_head')
    @stack('header')
</head>
<body class="disable-scrollbar {{@$is_home==1 ? 'homepage' : ''}}" >

@include('layouts.partials.internet_marketing_body')
@include('partials.header.header')

@yield('main')

@include('partials.footer.footer')
@include('partials.popups')
<script>
    window.public_key = "{{ config('recaptcha.api_site_key') }}";
    window.lang = '{{ $lang }}';
    /*window.addEventListener('livewire:navigated', () => {
        window.scrollTo({ top: 0 });
    });*/
</script>
<script src="/js/translation_{{\App::getLocale()}}.js"></script>

<script src="https://unpkg.com/imask"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.2.5/simplebar.min.js"></script> --}}
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

{{--
<script>
    var config = {
        container: '.validation',
        selectors: {
            error: 'error',
            messageError: 'msg-error'
        },
        messages: {
            required: 'Required',
            email: 'Invalid email',
            maxLength: 'Max 500 chars',
            pattern: 'Invalid data'
        },
        // onFormSubmit: function(container){
        //     sendAjaxForm(event,container);
        // }
    };
    var validator = new VanillaValidator(config);
</script> --}}

{!! setting('body') !!}


@yield('scripts')
@stack('footer-styles')
@stack('footer-scripts')

{{--@include('partials.quick_edit')--}}

</body>

</html>


