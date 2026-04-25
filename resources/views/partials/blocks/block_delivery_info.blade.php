<div class="delivery__top-row p-24 mt-24">
    <p class="fw-600 flex v--start">@if($block->picture)<img src="{!! $block->picture->t('picture')  ?: '/assets/images/trans.svg' !!}" alt="">@endif{!! $block->h2->t('title') ?? $page->t('title') !!}</p>
    <div class="text mt-16">
        @if($block->description){!! $block->description->t('description') !!}@endif
    </div>
</div>
