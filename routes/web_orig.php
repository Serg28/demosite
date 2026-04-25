<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AnaliticController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthSocialiteController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CategoryProductRouteController;
use App\Http\Controllers\CharacteristicController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\Custom\CheckoutController as CheckoutControllerCustom;
use App\Http\Controllers\Custom\ProfileController as ProfileControllerCustom;
use App\Http\Controllers\Custom\SearchController as SearchControllerCustom;
use App\Http\Controllers\DiscountCardController;
use App\Http\Controllers\EasyPayController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FollowPriceController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SearchController;


//<--
//use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\XmlController;

//Карта сайта динамическая
//Route::get('sitemap.xml', [SitemapController::class, 'index']);
//Карты сайта статические в виде файлов cоздаются в app/Console/Commands/GenerateGoogleSitemapFiles.php по заданию

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        //'middleware' => ['localeCookieRedirect']
        //'middleware' => ['localizationRedirect']
    ],
    function () {
        //Фиды динамические
        //Route::get('xml-feed/{feed_name}-{id}.xml', [XmlController::class, 'index'])->name('xml_feed');

        //Фиды статические файлы, созданные в app/Console/Commands/GenerateXmlFeedFiles.php по заданию
        Route::get('/xml-feed/{feed_file}.xml', [XmlController::class, 'getXmlFile'])->name('xml_feed_file');

        //Custom
        Route::post('/search/cities', [CheckoutController::class, 'searchCities'])->name('search.cities');
        //

        //== для всех характеристик
        //Раскомментировать роуты в app/Providers/RouteServiceProvider.php и поправить на мультиязычность
        //Route::get('{characteristic:slug}', [CharacteristicController::class, 'getOptions'])->name('characteristic');
        //Route::get('{characteristic}/{characteristic_option:slug}',
        //==

        //Custom - только для брендов (на базе CharacteristicController)
        //Список брендов /brands - страница из дерева, BrandListController
        //Далее - для страницы одного бренда
        Route::get('/brand', function () {
            return redirect('/brands');
        });
        Route::get(
            '{characteristic:slug}',
            [BrandController::class, 'getOptions']
        )->name('characteristic')->where('characteristic', 'brand');
        Route::get(
            '/{characteristic}/{characteristic_option:slug}',
            [BrandController::class, 'getProducts']
        )->name('characteristic_option')->where('characteristic', 'brand');
        //==

        Route::group(['prefix' => 'checkout'], function () {
            //Route::get('/', [CheckoutController::class, 'index'])->name('checkout');
            //--Custom
            Route::get('/', [CheckoutControllerCustom::class, 'index'])->name('checkout')->middleware('no-cache');
            Route::post('/update/{cart_id}', [CheckoutControllerCustom::class, 'update'])->name('checkout.update');
            Route::post('/remove/{cart_id}', [CheckoutControllerCustom::class, 'remove'])->name('checkout.remove');
            //--
            //Route::post('/send', [CheckoutController::class, 'send'])->name('checkout.send');
            Route::post('/send', [CheckoutControllerCustom::class, 'send'])->name('checkout.send');
            Route::get('/complete', [CheckoutControllerCustom::class, 'complete'])->name('checkout.complete');
            //Route::get('/complete', [CheckoutController::class, 'complete'])->name('checkout.complete');
            //Route::post('/search/cities', [CheckoutController::class, 'searchCities'])->name('checkout.search.cities');
            //Route::post('/search/deliveries', [CheckoutController::class, 'getDelivery'])->name('checkout.search.delivery');
            //--Custom
            Route::post(
                '/search/cities',
                [CheckoutControllerCustom::class, 'searchCities']
            )->name('checkout.search.cities');
            Route::post(
                '/search/deliveries',
                [CheckoutControllerCustom::class, 'getDeliveries']
            )->name('checkout.search.delivery');
            //--
            //Route::post('/delivery/pointers', [CheckoutController::class, 'getDeliveryPickupPointers'])->name('checkout.delivery_pickup_pointers');
            //Route::post('/delivery/pointers-np', [CheckoutController::class, 'getDeliveryNPPointers'])->name('checkout.delivery_np_pointers');
            //Route::post('/check-promocode', [CheckoutController::class, 'checkPromoCode'])->name('checkout.check_promo_code');
            //--Custom
            Route::post('/delivery/pointers', [
                CheckoutControllerCustom::class,
                'getDeliveryPickupPointers',
            ])->name('checkout.delivery_pickup_pointers');
            Route::post(
                '/delivery/pointers-np',
                [CheckoutControllerCustom::class, 'getDeliveryNPPointers']
            )->name('checkout.delivery_np_pointers');
            Route::post(
                '/delivery/street-np',
                [CheckoutControllerCustom::class, 'getDeliveryNPStreets']
            )->name('checkout.delivery_np_streets');

            Route::post(
                '/delivery/all-pointers',
                [CheckoutControllerCustom::class, 'getDeliveryPointers']
            )->name('checkout.delivery_all_pointers');
            //Route::post('/delivery/search-pointers',
            //    [CheckoutControllerCustom::class, 'getSearchDeliveryPointers'])->name('checkout.delivery_search_pointers');
            Route::post(
                '/reset-delivery',
                [CheckoutControllerCustom::class, 'removeDelivery']
            )->name('checkout.reset_delivery');
            Route::post(
                '/search/payments',
                [CheckoutControllerCustom::class, 'getPayments']
            )->name('checkout.payments');
            Route::post(
                '/check-promocode',
                [CheckoutControllerCustom::class, 'checkPromoCode']
            )->name('checkout.check_promo_code');
            Route::post(
                '/reset-promocode',
                [CheckoutControllerCustom::class, 'resetPromoCode']
            )->name('checkout.reset_promo_code');
            Route::post('/get-cart', [CheckoutControllerCustom::class, 'getCart'])->name('checkout.get_cart');
            Route::post('/save-form', [CheckoutControllerCustom::class, 'saveForm'])->name('checkout.save_form');
            //--
            Route::post('/get-products', [CheckoutController::class, 'getProducts'])->name('checkout.get_products');

            Route::post(
                '/payment-forms',
                [CheckoutControllerCustom::class, 'paymentForms']
            )->name('checkout.payment_forms');
        });

        Route::group(['prefix' => 'payment'], function () {
            Route::get('/init/{order}', [PaymentController::class, 'init'])->name('payment.init')->withoutMiddleware(['firewall.all']);;
            Route::post('/confirm/{order}', [PaymentController::class, 'confirm'])->name('payment.confirm')->withoutMiddleware(['firewall.all']);
            Route::get('/result/{order}', [PaymentController::class, 'result'])->name('payment.result')->withoutMiddleware(['firewall.all']);
            Route::post('/result/{order}', [PaymentController::class, 'result'])->name('payment.result')->withoutMiddleware(['firewall.all']);
        });

        Route::get('/blog/{slug?}', [NewsController::class, 'page'])->name('blog');
        //Route::get('/blog/{category?}/{slug?}', [NewsController::class, 'subcatpage'])->name('subcatblog.page'); //для вложенных урл у страницы новости
        //--
        Route::get('/promotion/{promotion:slug}/{filter?}', [PromotionController::class, 'page'])->where('filter', '.*')->name('promotion');

        Route::post('/subscription', [SubscriptionController::class, 'send'])->name('subscription');

        //Route::post('/search/live', [SearchController::class, 'live'])->name('search.live');
        //Route::get('/search', [SearchController::class, 'index'])->name('search');
        //Custom
        Route::post('/search/live', [SearchControllerCustom::class, 'live'])->name('search.live');
        //Route::get('/search', [SearchControllerCustom::class, 'index'])->name('search');
        Route::get('/search/{sortshow?}', [SearchControllerCustom::class, 'index'])->name('search')->where(
            'sortshow',
            '.*'
        );
        //

        Route::group(['prefix' => 'like', 'middleware' => 'auth.user'], function () {
            Route::post('/add/{product}', [LikeController::class, 'add'])->name('like.add');
            Route::post('/delete/{product}', [LikeController::class, 'delete'])->name('like.delete');
            Route::post('/info', [LikeController::class, 'getInfo'])->name('like.info');
        });

        Route::group(['prefix' => 'compare'], function () {
            Route::get('/', [CompareController::class, 'index'])->name('compare');
            Route::post('/add/{product}', [CompareController::class, 'add'])->name('compare.add');
            Route::post('/delete/{product}', [CompareController::class, 'delete'])->name('compare.delete');
        });

        Route::post('/follow-the-price/{product}', [FollowPriceController::class, 'add'])->name('follow_price');
        Route::post('/buy-one-click/{product}', [BasketController::class, 'buyOneClick'])->name('order.buy_one_click');
        Route::post('/callback', [FeedbackController::class, 'send'])->name('callback');
        Route::post('/discount', [DiscountCardController::class, 'register'])->name('discount.register');
        Route::get(
            '/diskontna-programa/success',
            [DiscountCardController::class, 'success']
        )->name('discountcard.registered.success');

        Route::group(['prefix' => 'basket'], function () {
            Route::post('/get-cart', [BasketController::class, 'getCart'])->name('basket.get_cart');
            Route::post('/add/{product}', [BasketController::class, 'add'])->name('basket.add');
            Route::post('/add-multi', [BasketController::class, 'addMulti'])->name('basket.add_multi');
            Route::post('/remove/{cart_id}', [BasketController::class, 'remove'])->name('basket.remove');
            Route::post('/update/{cart_id}', [BasketController::class, 'update'])->name('basket.update');

            /*Route::post('/add-payparts/{product}', [BasketController::class, 'add'])->name('basket.add_payparts');
            Route::post('/remove-payparts/{cart_id}', [BasketController::class, 'remove'])->name('basket.remove_payparts');
            Route::post('/update-payparts/{cart_id}', [BasketController::class, 'update'])->name('basket.update_payparts');
            Route::post('/prices-payparts', [BasketController::class, 'paypartsPrices'])->name('basket.prices_payparts');*/
            Route::post(
                '/add/{product}/with-{payments}',
                [BasketController::class, 'add']
            )->name('basket.add_payparts');
            Route::post(
                '/remove/{cart_id}/with-{payments}',
                [BasketController::class, 'remove']
            )->name('basket.remove_payparts');
            Route::post(
                '/update/{cart_id}/with-{payments}',
                [BasketController::class, 'update']
            )->name('basket.update_payparts');
            Route::post(
                '/prices/with-{payments}',
                [BasketController::class, 'paypartsPrices']
            )->name('basket.prices_payparts');

            Route::post(
                '/view/{product}/with-{payments}',
                [BasketController::class, 'viewPaypartsForProduct']
            )->name('basket.view_payparts_product');
            Route::post(
                '/view/{product}/prices/with-{payments}',
                [BasketController::class, 'viewPaypartsPricesForProduct']
            )->name('basket.view_prices_payparts_product');
        });

        //Route::group(['prefix' => 'profile', 'middleware' => 'auth.login'], function () { //редирект на страницу авторизации
        Route::group(['prefix' => 'profile', 'middleware' => 'auth.login.home'], function () { //редирект на главную
            //Route::get('/', [ProfileController::class, 'index'])->name('profile');
            //Route::get('/orders', [ProfileController::class, 'orders'])->name('profile.orders');
            //--Custom
            Route::get('/', [ProfileControllerCustom::class, 'index'])->name('profile');
            //Route::get('/orders', [ProfileControllerCustom::class, 'ordersUnFinished'])->name('profile.orders');
            Route::get('/orders', [ProfileControllerCustom::class, 'orders'])->name('profile.orders');
            Route::get('/orders/{id}', [ProfileControllerCustom::class, 'ordersPage'])->name('profile.orders.details');
            Route::get('/orders/{page}', [ProfileControllerCustom::class, 'orders'])->name('profile.orders.page');

            Route::get('/orders/completed',[ProfileControllerCustom::class, 'ordersFinished'])->name('profile.orders.completed');
            Route::get('/compare', [CompareController::class, 'index'])->name('profile.compare');
            //--
            Route::get('/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
            Route::get(
                '/viewed-products',
                [ProfileController::class, 'viewedProducts']
            )->name('profile.viewed_products');
            Route::get('/discount', [ProfileController::class, 'discount'])->name('profile.discount');
            Route::get('/logout', [ProfileController::class, 'logout'])->name('profile.logout');

            Route::post('/save-data', [ProfileController::class, 'save'])->name('profile.save');
            Route::post(
                '/save-password',
                [ProfileControllerCustom::class, 'saveProfilePassword']
            )->name('profile.save.password');

            Route::post('/photo/upload', [ProfileController::class, 'uploadPhoto'])->name('profile.upload_photo');
            Route::get('/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
            Route::get('/subscriptions', [ProfileController::class, 'subscriptions'])->name('profile.subscriptions');
            Route::get('/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');
            Route::get('/service', [ProfileController::class, 'service'])->name('profile.service');
        });

        Route::group(['prefix' => 'auth'], function () {
            Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
            Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot_password');
            Route::post('/registration', [AuthController::class, 'registration'])->name('auth.registration');
            Route::get(
                '/activating-user/{user}/{token}',
                [AuthController::class, 'activatingUser']
            )->name('auth.activating_user');

            Route::get('/login', [AuthController::class, 'pageLogin'])->name('auth.page.login');
            Route::get('/registration', [AuthController::class, 'pageRegistration'])->name('auth.page.registration');
            Route::get(
                '/forgot-password',
                [AuthController::class, 'pageForgotPassword']
            )->name('auth.page.forgot-password');

            Route::get('/facebook', [AuthSocialiteController::class, 'facebook'])->name('auth.facebook');
            Route::get(
                '/facebook/callback',
                [AuthSocialiteController::class, 'facebookCallback']
            )->name('auth.facebook.callback');

            Route::get('/google', [AuthSocialiteController::class, 'google'])->name('auth.google');
            Route::get(
                '/google/callback',
                [AuthSocialiteController::class, 'googleCallback']
            )->name('auth.google.callback');
        });

        Route::group(['prefix' => 'analitics'], function () {
            //Route::post('/click-product/{product}', [ProductController::class, 'analiticClickProduct'])->name('analitic.click_product');
            Route::post(
                '/checkout/analitic-data',
                [CheckoutControllerCustom::class, 'analiticData']
            )->name('analitic.checkout_data');
        });

        Route::post('/comment/{product}/send', [CommentController::class, 'send'])->name('comment');

        Route::get(
            '/change-products-view/{view}',
            [CategoryController::class, 'changeProductsView']
        )->name('change.products.view');

        Route::post('/product-availability', [OrderController::class, 'availability'])->name('product.availability');

        Route::get('/easypay/success', [EasyPayController::class, 'success'])->name('easypay.success');
        Route::get('/easypay/error', [EasyPayController::class, 'error'])->name('easypay.error');


        //Ajax
        Route::group(['prefix' => 'ajax'], function () {
            Route::get('/menucatalog', [AjaxController::class, 'menuCatalog'])->name('ajax.menucatalog');
        });

        Route::group(['prefix' => 'vacancies'], function () {
            Route::get('/', [VacancyController::class, 'index'])->name('vacancies.index');
            Route::get('/{page?}', [VacancyController::class, 'page'])->name('vacancies.page');
        });

//        Route::group(['prefix' => 'credit'], function () {
//            Route::get('/', [CreditController::class, 'index'])->name('credit.index');
//            Route::get('/{page?}', [CreditController::class, 'page'])->name('credit.page');
//        });
    }
);


Route::get('/{slug?}', [CategoryProductRouteController::class, 'route'])->where('slug', '.*')->name('catalog');


/*Route::get('/{any}',  function () {
    abort(404);
})->name('any')->where('any', '.*');*/

//Route::get('/{any}', [
//    CategoryProductController::class,
//    'index',
//])->/*middleware('cache.headers:etag')->middleware('request.cache:any')->*/ name('category_product')
//->where('any', '^(?!.*\.(css|js|png|jpg|jpeg|gif|webp|svg|ico|pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|tar|gz|html)$).*$');

