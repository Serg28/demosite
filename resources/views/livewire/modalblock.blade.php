<div>

    @if($class)
        <div class="img">
            <img src="/assets/images/{!! $class !!}.svg" alt="" class="mt-48 ml-auto mr-auto">
        </div>
    @endif
    <p class="fsz-20 fw-600 text--center mt-24">{!! $title !!}</p>
    <p class="mt-8 text--center">{!! $text !!}</p>
</div>
