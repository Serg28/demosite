@include('xml.partials.header')
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="{{ date('Y-m-d H:i') }}">
    <shop>
        <name>{{ config('app.name') }}</name>
        <company>{{ config('app.name') }}</company>
        <url>{{  config('app.url') }}</url>
        <currencies>
            <currency id="UAH" rate="1"/>
        </currencies>
        <categories>
            @foreach($categories as $category)
                <category id="{{ $category->id }}">
                    {{ $category->t('title') }}
                </category>
            @endforeach
        </categories>
        <offers>
            @foreach($products as $product)
                <offer id="{{ $product->id }}" available="true">
                    <url>{{ $product->getUrl() }}</url>
                    <price>{{ $product->getPrice() }}</price>
                    @if ($product->getPriceOld())
                        <price_old>{{ $product->getPriceOld() }}</price_old>
                    @endif
                    <currencyId>UAH</currencyId>
                    <categoryId>{{ $product->category->id }}</categoryId>
                    <picture>{{ $product->getPicture() }}</picture>
                    @include('xml.partials.other_images', ['product' => $product, 'image' => 'picture'])
                    @include('xml.partials.vendor', ['tag' => 'vendor'])
                    <stock_quantity>{{ $product->quantity }}</stock_quantity>
                    <name><![CDATA[{{ $product->getTextFromJson($product->title, 'ru') . ' ' . __t('shop') . ' (' . $product->getArticle() . ')' }}]]></name>
                    <name_ua><![CDATA[{{ $product->getTextFromJson($product->title, 'ua') . ' ' . __t('shop') . ' (' . $product->getArticle() . ')' }}]]></name_ua>
                    <description><![CDATA[{{ strip_tags($product->getTextFromJson($product->description, 'ru')) }}]]></description>
                    <description_ua><![CDATA[{{ strip_tags($product->getTextFromJson($product->description, 'ua')) }}]]></description_ua>
                    <state>{{ $product->isNew() }}></state>
                    {{--  Мінімальна кількість необхідних характеристик для товару — 3 --}}
                    @include('xml.partials.characteristics', ['product' => $product])
                </offer>
            @endforeach
        </offers>
    </shop>
</yml_catalog>
