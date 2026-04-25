<div class="product-wrap catalog-container ga4_item_list_name" data-item_list_name="Sale block">
    <div class="col-4-5 catalog-items shop_container_ lazy">
        @include('promotion.partials.products')
    </div>
    <div class="pagination-wrapper catalog-pagination paginate_link">
        @include('partials.paginate', ['items' => $products])
    </div>
</div>
