<div class="acc-section mt-16">
    <div class="top p-24">
        <p class="fsz-18 fw-600">{!! strip_tags($block->h2->t('title')) !!}</p>
        <span class="fsz-14 color--gray mt-4">{!! strip_tags($block->short_description->t('short_description')) !!}</span>
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 14 9" fill="none">
                <path d="M13 7.5L7 1.5L1 7.5" stroke="#2264DC" stroke-width="2"/>
            </svg>
        </div>
    </div>
    <div class="bottom p-24">
        {!! $block->description->t('description') !!}
    </div>
</div>