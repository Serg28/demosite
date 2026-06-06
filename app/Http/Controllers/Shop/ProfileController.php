<?php

namespace App\Http\Controllers\Shop;

use App\Actions\Order\InitiateOrderPayment;
use App\Actions\Order\RepeatOrder;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Services\BreadcrumbsService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProfileController extends Controller
{
    public function index(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.index', [
            'pageName'    => __t('Особистий кабінет'),
            'action'      => 'index',
            'breadcrumbs' => $breadcrumbs->forProfile(),
        ]);
    }

    public function orders(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.orders', [
            'pageName'    => __t('Мої замовлення'),
            'action'      => 'orders',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Мої замовлення')),
        ]);
    }

    public function ordersDetails(int $id, BreadcrumbsService $breadcrumbs): View
    {
        $order = Auth::user()->orders()
            ->with(['products.product', 'orderStatus', 'payMethod', 'delivery', 'paymentInvoices'])
            ->findOrFail($id);

        $timelineStatuses = OrderStatus::ordered()->where('is_in_timeline', true)->get();
        $currentPriority  = $order->orderStatus?->priority ?? 0;

        return view('profile.order', [
            'order'            => $order,
            'timelineStatuses' => $timelineStatuses,
            'currentPriority'  => $currentPriority,
            'orderStatus'      => $order->orderStatus,
            'pageName'          => __t('Замовлення') . ' #' . $id,
            'breadcrumbs'       => $breadcrumbs->simple(
                __t('Замовлення') . ' #' . $id,
                [
                    route('profile.index')  => __t('Кабінет'),
                    route('profile.orders') => __t('Мої замовлення'),
                ]
            ),
        ]);
    }

    public function repeatOrder(int $id): RedirectResponse
    {
        $order = Auth::user()->orders()->with('products')->findOrFail($id);

        try {
            $added = app(RepeatOrder::class)->handle($order, Auth::user());

            if ($added === 0) {
                return redirect()->route('profile.orders.details', $id)
                    ->with('warning', __t('Не вдалося додати товари — вони можуть бути видалені.'));
            }

            return redirect()->route('profile.orders.details', $id)
                ->with('success', __t('Товари з замовлення додано до кошика.'));

        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        }
    }

    public function payOrder(int $id): RedirectResponse
    {
        $order = Auth::user()->orders()
            ->with(['payMethod', 'paymentInvoices'])
            ->findOrFail($id);

        try {
            $paymentUrl = app(InitiateOrderPayment::class)->handle($order, Auth::user());

            return redirect()->away($paymentUrl);

        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (RuntimeException $e) {
            return redirect()->route('profile.orders.details', $id)
                ->with('error', $e->getMessage());
        } catch (Throwable) {
            return redirect()->route('profile.orders.details', $id)
                ->with('error', __t('Не вдалося ініціювати оплату. Спробуйте пізніше.'));
        }
    }

    public function security(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.security', [
            'pageName'    => __t('Безпека'),
            'action'      => 'security',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Безпека')),
        ]);
    }

    public function addresses(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.addresses', [
            'pageName'    => __t('Адреси доставки'),
            'action'      => 'addresses',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Адреси доставки')),
        ]);
    }

    public function recipients(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.recipients', [
            'pageName'    => __t('Отримувачі'),
            'action'      => 'recipients',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Отримувачі')),
        ]);
    }

    public function discounts(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.discounts', [
            'pageName'    => __t('Знижки та бонуси'),
            'action'      => 'discounts',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Знижки та бонуси')),
        ]);
    }

    public function favorites(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.favorites', [
            'pageName'    => __t('Список бажань'),
            'action'      => 'favorites',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Список бажань')),
        ]);
    }

    public function compare(BreadcrumbsService $breadcrumbs): View
    {
        return view('profile.compare', [
            'pageName'    => __t('Порівняння'),
            'action'      => 'compare',
            'breadcrumbs' => $breadcrumbs->forProfile(__t('Порівняння')),
        ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect('/');
    }
}
