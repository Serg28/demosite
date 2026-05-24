<?php

namespace App\Http\Middleware;

use App\Services\Cart\UnfinishedBasketService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Linecore\Shoppingcart\Cart;

/**
 * Відновлює кошик із "кинутого кошика" (cookies), якщо поточний кошик порожній.
 *
 * Пропускає: admin/*, api/*, livewire/update, статичні файли.
 */
class RecoverBasketFromCookies
{
    public function __construct(
        private readonly UnfinishedBasketService $unfinishedBasketService,
        private readonly Cart $cart,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $adminPrefix = config('cms.cms.admin_prefix', 'admin');

        if (
            $this->shouldIgnore($request)
            || $request->is('api/*')
            || $request->is($adminPrefix)
            || $request->is($adminPrefix.'/*')
        ) {
            return $next($request);
        }

        if ($this->cart->instance('default')->count() === 0) {
            // Кешуємо на 5хв: не робимо DB-запит на кожен запит при порожньому кошику
            $cacheKey = 'basket_recovered_'.(string) $request->cookie('unfinished_basket', 'none');
            if (Cache::has($cacheKey)) {
                return $next($request);
            }

            $products = $this->unfinishedBasketService->getProducts();
            $restored = false;

            foreach ($products as $product) {
                $productModel = $product->product;

                if (! $productModel || empty($product->count) || ! is_numeric($product->count)) {
                    continue;
                }

                $options = is_string($product->options)
                    ? json_decode($product->options, true) ?? []
                    : (array) $product->options;

                $this->cart->add(
                    $productModel->id,
                    $productModel->t('title'),
                    $product->count,
                    $productModel->price,
                    0,
                    $options
                )->associate($productModel);

                $restored = true;
            }

            // Якщо нема що відновлювати — кешуємо щоб не повторювати запит
            if (! $restored) {
                Cache::put($cacheKey, true, 300);
            }
        }

        return $next($request);
    }

    protected function shouldIgnore(Request $request): bool
    {
        return $request->is(
            'account/login',
            'account/register',
            'account/forgot-password',
            'account/reset-password/*',
            'account/otp',
            'livewire/update',
        );
    }
}
