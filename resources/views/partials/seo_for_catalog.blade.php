@if (isset($page) && $page)
    <title>{{$page->getSeoTitle($filter)}}</title>
    <meta name="description" content="{{$page->getSeoDescription($filter)}}"/>
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{request()->url()}}">
    @if (setting('twitter_account'))
        <meta name="twitter:creator" content="{{ setting('twitter_account') }}">
        <meta name="twitter:card" content="{{ $page->getSeoPicture()}}">
    @endif
    <meta property="og:title" content="{{$page->getSeoTitle($filter)}}">
    <meta property="og:description" content="{{$page->getSeoDescription($filter)}}">
    <meta property="og:image" content="{{ $page->getSeoPicture()}}">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="314">

    @if (($page->seo && $page->seo->is_seo_noindex) || $filter->getCountFilterSelected() > 1 || empty($count)  || config('app.domain')!==request()->getHost())
        <meta name="robots" content="noindex,nofollow"/>
    @endif

    <link rel="canonical" href="{{$filter->getCategoryUrl()}}"/>

    @foreach($languages as $language)
        <link rel="alternate" hreflang="{{ $language == 'ua' ? 'uk' : $language }}-ua"
              href="{{geturl($page->getUrl($language), $language)}}"/>
    @endforeach
@endif
