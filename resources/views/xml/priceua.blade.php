@include('xml.partials.header')
<price date="{{ date('Y-m-d H:i:s') }}">
    <name>{{config('app.name')}}</name>
    <catalog>
        @foreach($categories as $category)
            <category id="{{ $category->id }}" @if($category->parent_id) parentID="{{ $category->parent_id }}" @endif>
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
                @if ($product->getPriceOld())
                    <oldprice>{{ $product->getPriceOld() }}</oldprice>
                @endif
                <url>{{ $product->getUrl() }}</url>
                <image>{{ $product->getPicture() }}</image>
                @include('xml.partials.vendor', ['tag' => 'vendor'])
                <code>{{$product->getArticle() }}</code>
                <description><![CDATA[{{ $product->withoutTag() }}]]></description>
                <guarantee type="{{ $product->guarantee_type }}">{{ $product->guarantee }}</guarantee>
                @include('xml.partials.characteristics', ['product' => $product])
            </item>
        @endforeach
    </items>
</price>
