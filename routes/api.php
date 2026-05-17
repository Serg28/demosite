<?php

use App\Http\Controllers\Api\v1\CatalogController;
use App\Http\Controllers\Api\v1\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/checkout/cities', [CheckoutController::class, 'searchCities'])->name('api.v1.checkout.cities');
    Route::get('/checkout/warehouses', [CheckoutController::class, 'searchWarehouses'])->name('api.v1.checkout.warehouses');
    Route::get('/checkout/deliveries', [CheckoutController::class, 'deliveriesForCity'])->name('api.v1.checkout.deliveries');
    Route::get('/checkout/payments', [CheckoutController::class, 'paymentsForDelivery'])->name('api.v1.checkout.payments');

    Route::get('/catalog/{category}/facets', [CatalogController::class, 'getFacets']);
    Route::get('/catalog/{category}/facets/{characteristicId}/expanded', [CatalogController::class, 'getExpandedFacet']);
    Route::get('/catalog/options/{characteristicId}/search', [CatalogController::class, 'searchOptions']);
    Route::get('/catalog/options/{characteristicId}/range-stats', [CatalogController::class, 'getRangeStats']);
    Route::get('/catalog/products', [CatalogController::class, 'getProducts']);
    Route::get('/catalog/{category:slug}/products-html', [CatalogController::class, 'getProductsHtml'])->name('api.v1.catalog.products-html');
});
