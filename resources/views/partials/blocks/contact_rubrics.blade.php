
<div class="contacts__top-wrap p-24 mt-24">
    <div class="row flex v--start">
        <div class="icon flex v--center h--center"><img src="{{$phone['picture']}}" alt=""></div>
        <div class="right">
            <span class="fsz-14 color--gray">{{$phone['title']}}</span>
            <div class="tel-columns flex v--start h--wrap mt-12">

                @foreach($phone['list'] as $item)
                <a href="tel:{{$item->t('title')}}" class="col fsz-16 fw-600 color--black">{{$item->t('title')}}</a>
                @endforeach

            </div>
            <p class="fsz-13 mt-12 color--gray">{!! setting('grafik-raboty-v-kontaktah') !!}</p>
            <p class="fsz-14 mt-12 color--gray">{{$social['title']}}</p>
            <div class="socs-wrap mt-12 flex v--center">
                @foreach($social['list'] as $item)
                    <a href="{{$item->t('description')}}" class="socs flex v--center h--center" target="_blank"><img src="{{$item->getImgPath(24, 24)}}" width="24" height="24" alt=""></a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="row flex v--start mt-24">
        <div class="icon flex v--center h--center"><img src="{{$email['picture']}}" alt=""></div>
        <div class="right">
            <p class="fsz-14 color--gray">{{$email['title']}}</p>
            @foreach($email['list'] as $item)
                <a href="mailto:{{$item->t('title')}}" class="mail fw-600 color--black">{{$item->t('title')}}</a>
            @endforeach
            <p class="fsz-13 mt-12 color--gray">{{__t('З питань співпраці звертайтесь на пошту ')}}</p>
        </div>
    </div>
</div>