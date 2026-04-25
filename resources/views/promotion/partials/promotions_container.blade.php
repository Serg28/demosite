<div class="sale-block catalog-container">
    <div class="col-2 catalog-items lazy">
        @include('promotion.partials.promotions')
    </div>
    <div class="pagination-wrapper catalog-pagination paginate_link">
        @include('partials.paginate', ['items' => $promotions])
    </div>
</div>
