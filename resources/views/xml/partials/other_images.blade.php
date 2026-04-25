@if($product->other_pictures)
    @foreach($product->getOtherImgWithOriginal("other_pictures", ['w' => 500, 'h'=>500]) as  $pictureSmall)
        <{{$image}}>{{ request()->getSchemeAndHttpHost() .$pictureSmall }}</{{$image}}>
    @endforeach
@else
    <{{$image}}></{{$image}}>
@endif
