@foreach($product->characteristics as $characteristic)
    @if(array_key_exists($characteristic->characteristic->slug, $params))
        <{{$params[$characteristic->characteristic->localizedSlug]}}>{{ $characteristic->characteristicOption->t('title') }}</{{$params[$characteristic->characteristic->localizedSlug]}}>
    @endif
@endforeach
