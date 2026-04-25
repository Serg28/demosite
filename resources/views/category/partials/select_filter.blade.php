{{-- Обрабатываетс в композере app/Http/ViewComposers/FilterSelectComposer.php --}}
@foreach($filters as $k=>$option)
    <div class="label flex v--center">{{$option->t('title')}} <a href="{{$filter->urlFilter($option)}}" class="icon"><img src="/assets/images/close.svg" alt=""></a></div>
@endforeach