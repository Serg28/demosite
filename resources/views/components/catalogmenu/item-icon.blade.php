@if($picture)
    <span class="icon">
        <img loading="lazy" src="{{ $picture }}" alt="{{ $alt }}" width="{{$width}}" height="{{$height}}" />
    </span>
@endif