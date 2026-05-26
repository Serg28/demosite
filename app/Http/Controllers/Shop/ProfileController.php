<?php

namespace App\Http\Controllers\Shop;

use App\Actions\Order\InitiateOrderPayment;
use App\Actions\Order\RepeatOrder;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('profile.index', [
            'pageName' => 'Персональні дані',
            'action'   => 'index',
        ]);
    }

    public function orders(): View
    {
        return view('profile.orders', [
            'pageName' => 'Замовлення',
            'action'   => 'orders',
        ]);
    }

    public function ordersDetails(int $id): View
    {
        $order = Auth::user()->orders()
            ->with(['products', 'orderStatus', 'payMethod', 'delivery', 'paymentInvoices'])
            ->findOrFail($id);

        return view('profile.order', [
            'order'    => $order,
            'pageName' => 'Замовлення',
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

    public function security(): View
    {
        return view('profile.security', [
            'pageName' => 'Безпека',
            'action'   => 'security',
        ]);
    }

    public function addresses(): View
    {
        return view('profile.addresses', [
            'pageName' => 'Адреси доставки',
            'action'   => 'addresses',
        ]);
    }

    public function recipients(): View
    {
        return view('profile.recipients', [
            'pageName' => 'Отримувачі',
            'action'   => 'recipients',
        ]);
    }

    public function discounts(): View
    {
        return view('profile.discounts', [
            'pageName' => 'Знижки та бонуси',
            'action'   => 'discounts',
        ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect('/');
    }
}
