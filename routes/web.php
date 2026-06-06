<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Shop\CompareController;
use App\Http\Controllers\Shop\LikeController;
use App\Http\Controllers\Shop\ProfileController as ShopProfileController;
use App\Livewire\Compare\Lists as CompareLists;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Livewire;
use Livewire\Volt\Volt;

// Livewire update endpoint із захистом від ботів та ін'єкцій методів
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/livewire/update', $handle)
        ->middleware(['block.bot_request', 'validate.livewire.method']);
});

// Головна
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Каталог: category slug + wildcard filter-сегменти (Phase 3.7)
Route::get('/catalog/{category}/{filters?}', [CatalogController::class, 'show'])
    ->where(['category' => '[^/]+', 'filters' => '.*'])
    ->name('catalog.show');

// Товар: /product/{slug} (Phase 5.1)
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Кошик (Phase 4)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/add-all', [CartController::class, 'addBulk'])->name('add-all');
    Route::patch('/update/{rowId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{rowId}', [CartController::class, 'remove'])->name('remove');
});

// Оформлення замовлення (Phase 4.3)
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// Payment webhooks (Phase 4.6) — без CSRF, без auth, HTTP 200 завжди
Route::withoutMiddleware([ValidateCsrfToken::class])->prefix('payment')->name('payment.')->group(function () {
    Route::post('/webhook/paylink', [PaymentWebhookController::class, 'handlePayLink'])->name('webhook.paylink');
    Route::post('/webhook/{gateway}', [PaymentWebhookController::class, 'handle'])->name('webhook');
});

// Вибране (Phase 5.5)
Route::prefix('like')->name('like.')->group(function () {
    Route::post('/toggle/{product}', [LikeController::class, 'toggle'])->middleware('auth')->name('toggle');
    Route::post('/status', [LikeController::class, 'status'])->name('status');
});

// Порівняння (Phase 5.5) — доступне анонімним
Route::prefix('compare')->name('compare.')->group(function () {
    Route::get('/', CompareLists::class)->name('index');
    Route::post('/add/{product}', [CompareController::class, 'add'])->name('add');
    Route::post('/delete/{product}', [CompareController::class, 'delete'])->name('delete');
    Route::post('/status', [CompareController::class, 'status'])->name('status');
});

// Профіль (Phase 5.2+)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ShopProfileController::class, 'index'])->name('index');
    Route::get('/orders', [ShopProfileController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [ShopProfileController::class, 'ordersDetails'])->name('orders.details');
    Route::post('/orders/{id}/repeat', [ShopProfileController::class, 'repeatOrder'])->name('orders.repeat');
    Route::post('/orders/{id}/pay', [ShopProfileController::class, 'payOrder'])->name('orders.pay');
    Route::get('/security', [ShopProfileController::class, 'security'])->name('security');
    Route::get('/addresses', [ShopProfileController::class, 'addresses'])->name('addresses');
    Route::get('/recipients', [ShopProfileController::class, 'recipients'])->name('recipients');
    Route::get('/discounts', [ShopProfileController::class, 'discounts'])->name('discounts');
    Route::get('/favorites', [ShopProfileController::class, 'favorites'])->name('favorites');
    Route::get('/compare', [ShopProfileController::class, 'compare'])->name('compare');
    Route::get('/logout', [ShopProfileController::class, 'logout'])->name('logout');
});

// Налаштування (Fortify/Volt)
Route::middleware(['auth'])->prefix('settings')->group(function () {
    Route::redirect('/', 'settings/profile');
    Volt::route('profile', 'settings.profile')->name('profile.edit');
    Volt::route('password', 'settings.password')->name('password.edit');
    Volt::route('appearance', 'settings.appearance')->name('appearance.edit');
    Volt::route('two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Акаунт (редиректи + auth routes)
Route::prefix('account')->group(function () {
    Route::get('/', fn () => auth()->check()
        ? redirect()->route('profile.index')
        : redirect()->route('login')
    );

    require __DIR__.'/auth.php';
});
