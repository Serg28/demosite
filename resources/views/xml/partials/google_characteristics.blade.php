@foreach($product->characteristics as $characteristic)
    @if(array_key_exists($characteristic->characteristic->slug, $params))
        @if($characteristic->characteristic->slug=='model')
            <{{$params[$characteristic->characteristic->slug]}}>{{ Str::limit($characteristic->characteristicOption->t('title'), 50) }}</{{$params[$characteristic->characteristic->slug]}}>
        @elseif($characteristic->characteristic->slug=='stat')
            @if(in_array($characteristic->characteristicOption->slug, ['dlya-divchat','dlya-hlopchykiv' ,'pidlitkova', 'dytyacha']))
                <{{$params[$characteristic->characteristic->slug]}}>kids</{{$params[$characteristic->characteristic->slug]}}>
            @elseif(in_array($characteristic->characteristicOption->slug, ['zhinocha','cholovicha' ,'uniseks']))
                <g:gender>{{$gender[$characteristic->characteristicOption->slug]}}</g:gender>
            @endif
        @else
            <{{$params[$characteristic->characteristic->slug]}}>{{ $characteristic->characteristicOption->t('title') }}</{{$params[$characteristic->characteristic->slug]}}>
        @endif
    @endif
@endforeach
