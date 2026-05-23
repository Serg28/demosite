<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
            ->with(['products', 'orderStatus', 'payMethod', 'delivery'])
            ->findOrFail($id);

        return view('profile.order', [
            'order'    => $order,
            'pageName' => 'Замовлення',
        ]);
    }

    public function security(): View
    {
        return view('profile.security', [
            'pageName' => 'Безпека',
            'action'   => 'security',
        ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect('/');
    }
}
