<?php /** @var \App\Models\Product $product */ ?>
@foreach($products as $product)
    <a target="_blank" href="{!! $product->getUrl() !!}?show=1">{!!$product->t('title') !!}</a>,
@endforeach
