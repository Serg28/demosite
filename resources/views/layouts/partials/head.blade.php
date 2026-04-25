<meta name="format-detection" content="telephone=no">
<meta charset="UTF-8">
@yield('seo_tags')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--<meta name="robots" content="noindex"> --}}
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.2.5/simplebar.min.css">
<link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
<link rel="stylesheet" type="text/css" href="{{ mix('/assets/css/main.min.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}"/>

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<link rel="dns-prefetch preconnect" href="https://fonts.gstatic.com">
<link rel="dns-prefetch preconnect" href="https://fonts.googleapis.com">


{!! setting('js-kod-v-head') !!}
