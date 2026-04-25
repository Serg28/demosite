<div class="container">
    <div class="single-post__wrap flex v--start h--between">
        <div class="left">
            <h2 class="fsz-32 fw-600 heading">{{$page->t('title')}}</h2>
            <span class="mt-16 fsz-13 color--gray">{{$page->created_at->format('d.m.Y')}}</span>
            @if ($page->user_id)
                <meta itemprop="author" content="{{ $page->getAuthor() }}" />
            @endif
            <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization" style="display:none" >
                <meta itemprop="name" content="{{ $page->getSeoH1() }}" />
                <link itemprop="sameAs" href="{{$page->getUrl()}}" />
                <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                    <link itemprop="url image" href="/assets/images/logo.png" />
                </div>
            </div>

            <div class="content flex fd--column mt-24">
                {!! $page->t('description') !!}
            </div>
        </div>
        @include('news.partials.side-bar',['list' => $newsLast, 'count' => count($newsLast)])
    </div>
</div>
