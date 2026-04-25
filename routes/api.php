<?php

use App\Http\Controllers\Api\v1\CatalogController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\CleanController;
use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Общий контроллер
//Route::get('/example-news', 'ApiExamplesController@news');
//Route::get('/example-products', 'ApiExamplesController@products');
//Route::get('/example-categories', 'ApiExamplesController@categories');
//Route::get('/news', 'Api1cController@news');
// --

Route::middleware(['auth_api:api', 'firewall.all'])->group(function () {
    //Route::post('/products-update', [ProductController::class, 'updateList'])->name('product.update.list');

    Route::get('/products/rebuild', [ProductController::class, 'reindexUpdatedProducts'])->name('products.reindex-updated'); //Переиндексация измененных товаров
    Route::get('/products/reindex', [ProductController::class, 'reindex'])->name('products.reindex'); //Полная переиндексация
    Route::post('/products/available', [ProductController::class, 'updateAvailableList'])->name('product.update.available.list'); //Статус товара и дата поступления
    Route::post('/products/update', [ProductController::class, 'updateList'])->name('product.update.list'); //Любые поля товара
    Route::post('/products/prices', [ProductController::class, 'updatePricesList'])->name('product.update.prices.list'); // Старая и новая цены
    Route::post('/products/payparts', [ProductController::class, 'updatePayPartsList'])->name('product.update.payparts.list'); // Старая и новая цены
    Route::apiResource('products', ProductController::class);

    //Глобальный кеш
    Route::get('/clean-cache', [CleanController::class, 'cleanCache'])->name('cache.clean');

    //Категории
    Route::get('/categories/rebuild', [CategoryController::class, 'rebuild'])->name('categories.rebuild');
    //Route::delete('/categories', [CategoryController::class, 'truncate'])->name('categories.truncate');
    Route::apiResource('categories', CategoryController::class);

    //----Каталог товаров
    //Обслуживание каталога при изменении опций, характеристик
    Route::get('/catalog/rebuild', [CatalogController::class, 'rebuild'])->name('catalog.rebuild');
});
