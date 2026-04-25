@include('xml.partials.header')
<shop>
    <name>{{ config('app.name') }}</name>
    <catalog>
        @foreach($categories as $category)
            <category id="{{ $category->id }}"  @if($category->parent_id) parentId="{{ $category->parent_id }}" @endif>
                {{ $category->t('title') }}
            </category>
        @endforeach
    </catalog>
    <items>
        @foreach($products as $product)
            <item id="{{ $product->id }}">
                <name><![CDATA[{{ $product->t('title') }}]]></name>
                <categoryId>{{ $product->category->id }}</categoryId>
                <price>{{ $product->getPrice() }}</price>
                <priceuah>{{ $product->getPrice() }}</priceuah>
                <url>{{ $product->getUrl() }}</url>
                <image>{{ $product->getPicture() }}</image>
                {{--@include('xml.partials.vendor', ['tag' => 'vendor'])--}}
                <barcode>{{  $product->getArticle() }}</barcode>
                <description><![CDATA[{{ $product->withoutTag() }}]]></description>
                @if($product->seo && $product->seo->t('seo_keywords'))
                    <keywords>{{$product->seo->t('seo_keywords') }}</keywords>
                @endif
                @include('xml.partials.prom_characteristics', ['params' => ['brand'=>'vendor']])
            </item>
        @endforeach
    </items>
</shop>
