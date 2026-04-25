<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ImportProductOptionsController;
use App\Http\Controllers\Admin\ImportProductsController;
use App\Http\Controllers\Admin\LiqPayOnlineController;
use App\Http\Controllers\Admin\MonoPayPartsController;
use App\Http\Controllers\Admin\NovaPoshtaController;
use App\Http\Controllers\Admin\UnFinishBasketController;
use App\Http\Controllers\AdminController;

Route::post('/get-option-characteristic/{characteristic}', [AdminController::class, 'getCharacteristic']);
Route::post('/get-tree-for-menu/show-menu', [AdminController::class, 'getTreeForMenu']);
Route::get('/send/order/{id}/{type?}', [AdminController::class, 'sendToCheckbox']);
Route::get('/set-manager/{order}', [AdminController::class, 'setManager'])->name('set_manager');
Route::post('/products/import', [ImportProductsController::class]);
Route::post('/products/import_options', [ImportProductOptionsController::class]);

Route::post('/get-order/price-by-date', [DashboardController::class, 'getOrderPriceByDate']);
Route::post('/characteristics/add-new-option', [AdminController::class, 'createNewOption']);

Route::get('/create-order-from-unfinished-basket/{unfinished_basket}', [UnFinishBasketController::class, 'createOrder']);

Route::get('/export/unfinished-basket', [ExportController::class, 'unfinishedBaskets'])->name('admin.unfinished_basket.export');
Route::get('/export/products', [ExportController::class, 'products'])->name('admin.products.export');
Route::get('/export/product_options', [ExportController::class, 'productOptions'])->name('admin.products.options.export');

Route::get('/printing/{order}', [DocumentController::class, 'printing'])->name('document.printing');
Route::get('/receipt/{order}', [DocumentController::class, 'receipt'])->name('document.receipt');
Route::get('/invoice/{order}', [DocumentController::class, 'invoice'])->name('document.invoice');
Route::get('/account/{order}', [DocumentController::class, 'account'])->name('document.account');
Route::get('/email/{order}', [DocumentController::class, 'email'])->name('document.email');
Route::get('/email/changed/{order}', [DocumentController::class, 'orderChangedEmail'])->name('document.order.changed.email');
Route::get('/sms/{order}', [DocumentController::class, 'sms'])->name('document.sms');
//Route::get('/orderedit/{order}', [DocumentController::class, 'order'])->name('order');

Route::get('/np/form/{order}', [NovaPoshtaController::class, 'form'])->name('novaposhta.form');
Route::post('/np/form/create', [NovaPoshtaController::class, 'store'])->name('novaposhta.store');
Route::post('/np/form/update', [NovaPoshtaController::class, 'update'])->name('novaposhta.update');
Route::post('/np/form/delete', [NovaPoshtaController::class, 'delete'])->name('novaposhta.delete');
Route::get('/np/tracking/{order}', [NovaPoshtaController::class, 'tracking'])->name('novaposhta.tracking');
Route::get('/np/print/{order}/{type}', [NovaPoshtaController::class, 'print'])->name('novaposhta.print');
Route::get('/np/sender_warehouses/', [NovaPoshtaController::class, 'getDepartments'])->name('novaposhta.sender.warehouses');
Route::get('/np/sender_cities/', [NovaPoshtaController::class, 'getCities'])->name('novaposhta.sender.cities');

Route::post('/liqpayonline/hold-completion/{order}', [LiqPayOnlineController::class, 'holdcompletion'])->name('liqpayonline.holdcompletion');
Route::post('/liqpayonline/refund/{order}', [LiqPayOnlineController::class, 'refund'])->name('liqpayonline.refund');
Route::post('/liqpayonline/email-newlink/{order}', [LiqPayOnlineController::class, 'newlink'])->name('liqpayonline.email.newlink');

Route::post('/monopayparts/reject/{order}', [MonoPayPartsController::class, 'reject'])->name('monopayparts.reject');
Route::post('/monopayparts/return/{order}', [MonoPayPartsController::class, 'return'])->name('monopayparts.return');
Route::post('/monopayparts/confirmation/{order}', [MonoPayPartsController::class, 'confirmation'])->name('monopayparts.confirmation');

Route::post('/xml_feeds/load_options', [AdminController::class, 'xmlFeedLoadOption']);

Route::get('/load_order_logs/{order}', [AdminController::class, 'loadOrderLogs']);

Route::get('/left-sidebar/{view?}', [AdminController::class, 'changeSideBar'])->name('change.leftsidebar');

//Route::get('/rebuildtree', [CategoryController::class,'rebuildCatagories']);  //перестройка дерева категорий
