@include('xml.partials.header')
<price>
    <date>{{ date('Y-m-d H:i') }}</date>
    <firmName>{{ config('app.name') }}</firmName>
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
                @include('xml.partials.other_images', ['product' => $product, 'image' => 'image'])
                <priceRUAH>{{ $product->getPrice() }}</priceRUAH>
                @include('xml.partials.characteristics', ['product' => $product])
                <guarantee type="{{ $product->guarantee_type }}">{{ $product->guarantee }}</guarantee>
            </item>
        @endforeach
    </items>
</price>
