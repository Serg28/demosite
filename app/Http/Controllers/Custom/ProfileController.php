<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\ProfileController as BaseProfileController;
use App\Http\Requests\ProfilePassword;
use App\Models\CharacteristicOption;
use App\Services\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    public function index(): View {
        $pageName = __t('Персональні дані');
        $action = 'index';

        // Получение данных пользователя и преобразование в массив
        $user = app('user') ?: collect();

        return view('profile.index', compact('pageName',  'action', 'user'));
    }

    public function orders(): View {
        $action = "orders";
        $pageName = __t('Замовлення');
        return view('profile.orders', compact( 'pageName', 'action'));
        //return view('profile.orders', compact('orders', 'ordersFinished', 'ordersUnFinished', 'pageName', 'action'));
    }

    public function ordersPage($id = null): View
    {
        $user = app('user'); // Получаем текущего пользователя через app('user')
        $order = $user->orders()->findOrFail($id);

        $pageName = __t('Замовлення');
        return view('profile.order', compact('order', 'pageName'));
    }

    public function ordersUnFinished(): View
    {
        $ordersUnFinished = app('user')->orders()->with(['status', 'delivery', 'npWarehouse', 'deliveryPickupPoint','city.regions','city.settlements','products.product'])->where('order_status_id', '<=', 4)->latest()->paginate(10);
        $pageName = __t('Замовлення');

        return view('profile.orders', compact('ordersUnFinished', 'pageName'));
    }

    public function ordersFinished(): View
    {
        $ordersFinished = app('user')->orders()->with(['status', 'delivery', 'npWarehouse', 'deliveryPickupPoint', 'city.regions','city.settlements','products.product'])->where('order_status_id', '>', 4)->latest()->paginate(10);
        $pageName = __t('Замовлення');

        return view('profile.orders_completed', compact('ordersFinished', 'pageName'));
    }

    public function saveProfilePassword(ProfilePassword $request, Profile $profile): JsonResponse
    {
        return $profile->saveFormPassword($request);
    }
}
