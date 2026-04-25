@foreach($product->characteristics as $characteristic)
    <param name="{{ $characteristic->characteristic->t('title') }}">{{ $characteristic->characteristicOption->t('title') }}</param>
@endforeach
