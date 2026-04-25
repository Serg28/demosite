@if(@$is_home==1)
    <a class="logo flex v--center h--center">
        <picture>
            <source srcset="@if(!empty($img)){{$img}}@else /assets/images/logo.png @endif" media="(max-width: 1024px)">
            <img src="@if(!empty($img)){{$img}}@else /assets/images/logo.png @endif" data-alt="{{config('app.name')}}" alt="{{config('app.name')}}" class="lazy">
        </picture>
    </a>
@else
    <a itemprop="url" href="{{getUrl('/')}}" class="logo flex v--center h--center">
        <picture>
            <source srcset="@if(!empty($img)){{$img}}@else /assets/images/logo.png @endif" media="(max-width: 1024px)">
            <img src="@if(!empty($img)){{$img}}@else /assets/images/logo.png @endif" data-alt="{{config('app.name')}}" alt="{{config('app.name')}}" class="lazy">
        </picture>
    </a>
@endif
