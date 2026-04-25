<?php

use App\Http\Controllers\Api\v1\CatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/catalog/{category}/facets', [CatalogController::class, 'getFacets']);
    Route::get('/catalog/{category}/facets/{characteristicId}/expanded', [CatalogController::class, 'getExpandedFacet']);
    Route::get('/catalog/options/{characteristicId}/search', [CatalogController::class, 'searchOptions']);
    Route::get('/catalog/options/{characteristicId}/range-stats', [CatalogController::class, 'getRangeStats']);
    Route::get('/catalog/products', [CatalogController::class, 'getProducts']);
});
