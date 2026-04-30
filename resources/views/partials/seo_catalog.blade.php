{{-- SEO for catalog pages --}}
{{-- Variables: $page (Category with HasSeo), $count (int, total products), $seoTitle, $seoDescription --}}
{{-- $languages injected by SeoComposer --}}
@php
    $isStaging     = config('app.domain') !== request()->getHost();
    $filtersPath   = request()->route('filters', '');
    $isFilterPage  = str_contains((string) $filtersPath, '=');
    $hasNoProducts = empty($count);

    if (isset($page) && $page) {
        $seoTitle       = $page->getSeoTitle();
        $seoDescription = $page->getSeoDescription();
        $seoImage       = $page->getSeoPicture();
        $adminNoindex   = $page->getSeoNoindex();
        $adminNofollow  = $page->getSeoNofollow();
        // Canonical: explicit from DB, or clean category URL (without filter segments)
        $canonicalUrl   = $page->getSeoCanonical();
    } else {
        $seoTitle       = $seoTitle ?? config('app.name');
        $seoDescription = $seoDescription ?? '';
        $seoImage       = '';
        $adminNoindex   = false;
        $adminNofollow  = false;
        $canonicalUrl   = url()->current();
    }

    // Robots logic (priority: staging > no-products > filter-page > admin-flag)
    if ($isStaging) {
        $robotsTag = 'noindex, nofollow';
    } elseif ($hasNoProducts || $isFilterPage) {
        $robotsTag = 'noindex, follow';
    } elseif ($adminNoindex) {
        $noindex   = $adminNoindex ? 'noindex' : 'index';
        $nofollow  = $adminNofollow ? 'nofollow' : 'follow';
        $robotsTag = $noindex . ', ' . $nofollow;
    } else {
        $robotsTag = null;
    }
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

@if($robotsTag)
    <meta name="robots" content="{{ $robotsTag }}">
@endif

<link rel="canonical" href="{{ $canonicalUrl }}">

@if(isset($languages) && $languages->count() > 1)
    @foreach($languages as $language)
        <link rel="alternate" hreflang="{{ $language === 'ua' ? 'uk-ua' : $language . '-ua' }}"
              href="{{ geturl(currentUrl(), $language) }}">
    @endforeach
@endif
