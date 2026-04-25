@foreach($promotions as $promotion)
    {{--
        Дата начала {{$promotion->date_start}}
        Дата окончания {{$promotion->date_finish}}
        Дней осталось до окончания {{$promotion->time_left}}
        Дней осталось до начала {{$promotion->time_start}}
        Уже началась {{$promotion->time_started}}
        Уже закончилась {{$promotion->time_finished}}

        Форматированная дата старта {{$promotion->formateDate($promotion->date_start)}}
        Форматированная дата окончания {{$promotion->formateDate($promotion->date_finish)}}
    --}}

    <div class="col">
        <a href="{{$promotion->getUrl()}}" class="sale-img">
            <img src="{!! $promotion->getImgPath(600,390, ['fit'=>'crop']) !!}"
                 alt="{{$promotion->t('title')}}"
                 title="{{$promotion->t('title')}}" class="lazy" width="100%">
        </a>
        <div class="text-block">
            <a href="{{$promotion->getUrl()}}" class="sale-heading">{{$promotion->t('title')}}</a>
            @if(!empty($promotion->date_start) && $promotion->date_start !== '0000-00-00 00:00:00')
                <div class="sale-date">
                    @if($promotion->time_started && $promotion->time_finished)
                        <p>{{__t('Акция завершилась')}}</p>
                    @elseif($promotion->time_started && !$promotion->time_finished)
                        <p>{{__t('До конца акции')}} <strong>{{$promotion->time_left}} {{trans_choice(__t('{0}дней|[1]день|[2,4]дня|[5,*]дней'),$promotion->time_left)}}</strong></p>
                    @else
                        <p>{{__t('До начала акции')}} <strong>{{$promotion->time_start}} {{trans_choice(__t('{0}дней|[1]день|[2,4]дня|[5,*]дней'),$promotion->time_start)}}</strong></p>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endforeach
