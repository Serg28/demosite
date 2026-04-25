@include('xml.partials.header')
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ config('app.url') }}</link>
        <description>{{ $description }}</description>
        @foreach($products as $product)
            <item>
                {{--<g:id>{{ $product->id }}</g:id>--}}
                <g:id>{{ $product->code }}</g:id>
                <g:title>{{ $product->t('title') }}</g:title>
                <g:link>{{ $product->getUrl() }}</g:link>
                <g:description>
                    {{ Str::limit(strip_tags($product->t('description')),4900)}}
                </g:description>
                <g:condition>new</g:condition>
                @if($product->getPriceOld()>$product->getPrice())
                    <g:price>{{ $product->getPriceOld() }} {{ __t('UAH') }}</g:price>
                    <g:sale_price>{{ $product->getPrice() }} {{ __t('UAH') }}</g:sale_price>
                @else
                    <g:price>{{ $product->getPrice() }} {{ __t('UAH') }}</g:price>
                    <g:sale_price></g:sale_price>
                @endif
                {{--<g:availability>{{ $product->isStock(true) }}</g:availability> --}}
                <g:availability>{{ $product->status?->google_availability ?? '' }}</g:availability>

                {{--@include('xml.partials.vendor', ['tag' => 'g:brand'])--}}
                <g:image_link>{{ $product->getPicture() }}</g:image_link>
                @include('xml.partials.other_images', ['product' => $product, 'image' => 'g:additional_image_link'])
                <g:google_product_category>{{ $product->category->google_id}}</g:google_product_category>
                <g:product_type>{{ $product->category->t('title') }}</g:product_type>
                <g:mpn>{{ $product->code }}</g:mpn>
                <g:adult>no</g:adult>
                @include('xml.partials.google_characteristics', ['params' => ['kolir' => 'g:color','brand'=>'g:brand','rozmir' => 'g:size', 'model' => 'g:item_group_id', 'stat' => 'g:age_group'], 'gender' => ['zhinocha'=>'female', 'cholovicha' => 'male', 'uniseks' => 'unisex']])
                @if($product->mono || $product->part_pay)
                    <g:product_highlight>{{__t('Доступно в Оплату частями')}}</g:product_highlight>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
