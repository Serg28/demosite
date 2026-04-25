@include('xml.partials.header')
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ config('app.url') }}</link>
        <description>{{ __t("Facebook title xml") }}</description>
        @foreach($products as $product)
            <item>
                <id>{{ $product->id }}</id>
                <title>{{ $product->t('title') }}</title>
                <link>{{ $product->getUrl() }}</link>
                <description>{{ $product->withoutTag() }}</description>
                <condition>{{ $product->isNew() }}</condition>
                @if($product->getPriceOld())
                    <price>{{ $product->getPriceOld() }} {{ __t('UAH') }}</price>
                    <sale_price>{{ $product->getPrice() }} {{ __t('UAH') }}</sale_price>
                @else
                    <price>{{ $product->getPrice() }} {{ __t('UAH') }}</price>
                @endif
                <availability>{{ $product->isStock() }}</availability>
                @include('xml.partials.vendor', ['tag' => 'brand'])
                <image_link>{{ $product->getPicture() }}</image_link>
                @include('xml.partials.other_images', ['product' => $product, 'image' => 'additional_image_link'])
            </item>
        @endforeach
    </channel>
</rss>
