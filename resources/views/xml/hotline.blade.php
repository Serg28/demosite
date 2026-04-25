@include('xml.partials.header')
<price>
    <date>{{ date('Y-m-d H:i') }}</date>
    <firmName>{{ config('app.name') }}</firmName>
    {{--
    <categories>
        @foreach($categories as $category)
            <category>
                <id>{{ $category->id }}</id>
                @if($category->parent_id)
                    <parentId>{{ $category->parent_id }}</parentId>
                @endif
                <name>{{ $category->t('title') }}</name>
            </category>
        @endforeach
    </categories> --}}
    <categories>
        @foreach($categories as $category)
            <category>
                <id>{{ $category->id }}</id>
                @if($category->parent_id)
                    <parentId>{{ $category->parent_id }}</parentId>
                @endif
                <name>{{ $category->hotlineCategory?->t('name') ?? $category->t('title') }}</name>
            </category>
        @endforeach
    </categories>
    <items>
        @foreach($products as $product)
            <item>
                <id>{{ $product->id }}</id>
                <categoryId>{{ $product->category->id }}</categoryId>
                @include('xml.partials.vendor', ['tag' => 'vendor'])
                <name><![CDATA[{{ $product->t('title') }}]]></name>
                <description><![CDATA[{{ $product->withoutTag() }}]]></description>
                <url>{{ $product->getUrl() }}</url>
                <image>{{ $product->getPicture() }}</image>
                <priceRUAH>{{ $product->getPrice() }}</priceRUAH>
                <shipping>{{$product->status?->hotline_shipping ?? ''}}</shipping>
                <stock>{{str_replace('none', '', $product->status?->hotline_stock) ?? ''}}</stock>
            </item>
        @endforeach
    </items>
</price>
