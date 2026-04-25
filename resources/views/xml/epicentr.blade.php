@include('xml.partials.header')
<yml_catalog date="{{ date('Y-m-d H:i') }}">
    <offers>
        @foreach($products as $product)
            <offer id="{{ $product->id }}" available="true">
                <price>{{ $product->getPrice() }}</price>
                @if ($product->getPriceOld())
                    <price_old>{{ $product->getPriceOld() }}</price_old>
                @endif
                <category>{{ $product->category->t('title') }}</category>
                <picture>{{ $product->getPicture() }}</picture>
                @include('xml.partials.other_images', ['product' => $product, 'image' => 'picture'])
                @include('xml.partials.vendor', ['tag' => 'vendor'])
                <name lang="ua"><![CDATA[{{ $product->getTextFromJson($product->title, 'ua') }}]]></name>
                <name lang="ru"><![CDATA[{{ $product->getTextFromJson($product->title, 'ru') }}]]></name>
                <description lang="ua"><![CDATA[{{ strip_tags($product->getTextFromJson($product->short_description, 'ua')) }}]]></description>
                <description lang="ru"><![CDATA[{{ strip_tags($product->getTextFromJson($product->short_description, 'ru')) }}]]></description>
            </offer>
        @endforeach
    </offers>
</yml_catalog>
