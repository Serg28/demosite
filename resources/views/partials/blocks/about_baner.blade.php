<div class="baner">
    <h2 class="fsz-34 fw-600 color--white relative heading">{!! $block->h2->t('title') ?? $page->t('title') !!}</h2>
    <p class="mt-24 color--white relative sub-heading">@if($block->description){!! strip_tags($block->description->t('description')) !!}@endif</p>
    @if($block->picture)<img loading="lazy" src="{{$block->picture->getImgPath(415, 321, [], true) }}" alt="">@endif
</div>
