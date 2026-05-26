<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\Shop\ProfileController as ShopProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentWebhookController;
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

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Каталог: category slug + wildcard filter-сегменти (Phase 3.7)
Route::get('/catalog/{category}/{filters?}', [CatalogController::class, 'show'])
    ->where(['category' => '[^/]+', 'filters' => '.*'])
    ->name('catalog.show');

// Товар: /product/{slug} (Phase 5.1)
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
// Fallback одноуровневий /{slug} — для клієнтів без сегментів (СТАВИТИ ОСТАННІМ)
// Route::get('/{slug}', [CatalogController::class, 'routeSlug'])->where('slug', '[^/]+')->name('slug.route');

// Корзина (Phase 4)
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout (Phase 4.3)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Payment webhooks (Phase 4.6) — без CSRF, без auth, HTTP 200 завжди
Route::withoutMiddleware([ValidateCsrfToken::class])->group(function () {
    Route::post('/payment/webhook/paylink', [PaymentWebhookController::class, 'handlePayLink'])->name('payment.webhook.paylink');
    Route::post('/payment/webhook/{gateway}', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
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

// Профіль (Phase 5.2)
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
    Route::get('/logout', [ShopProfileController::class, 'logout'])->name('logout');
});

Route::prefix('account')->group(function () {
    // Авторизований → одразу в профіль
    Route::get('/', fn () => auth()->check()
        ? redirect()->route('profile.index')
        : redirect()->route('login')
    );

    require __DIR__.'/auth.php';
});
