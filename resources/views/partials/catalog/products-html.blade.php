<div data-fragment="products">
    @foreach($products as $product)
        @include('partials.catalog.product-card', compact('product'))
    @endforeach
</div>
@isset($paginator)
<div data-fragment="pagination">
    {{ $paginator->links('catalog.pagination') }}
</div>
@endisset
