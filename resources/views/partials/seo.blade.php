{{-- SEO, OG, canonical, noindex --}}
{{-- Variables: $seoTitle, $seoDescription, $seoImage, $seoNoindex --}}
@php
    $seoTitle      = $seoTitle ?? config('app.name');
    $seoDescription = $seoDescription ?? '';
    $seoImage      = $seoImage ?? '';
    $canonicalUrl  = url()->current();
    $queryWithoutPage = array_diff_key(request()->query(), ['page' => true]);
    $isFilterPage  = !empty($queryWithoutPage);
    $noindex       = $seoNoindex ?? $isFilterPage;
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">

<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
@if($seoImage)
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="314">
@endif

@if(config('app.env') !== 'production')
    <meta name="robots" content="noindex, nofollow">
@elseif($noindex)
    <meta name="robots" content="noindex, follow">
@endif

<link rel="canonical" href="{{ $canonicalUrl }}">
