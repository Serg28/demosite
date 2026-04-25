<div class="blog pb-80">
    <div class="container">
        <h2 class="fsz-34 fw-600 heading">{!! $page->getSeoH1() !!} @if(request('tag')) {{ __t('с тегом') }} «{{ $tagname->t('title') }}» @endif</h2>

        <livewire:news.news :pageId="$page->id"  />
    </div>
</div>