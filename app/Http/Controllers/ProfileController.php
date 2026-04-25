<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileData;
use App\Http\Requests\ProfilePassword;
use App\Http\Requests\UploadPhoto;
use App\Models\CumulativeDiscount;
use App\Models\Product;
use App\Services\Profile;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('profile.index', compact('user'));
    }

    public function uploadPhoto(UploadPhoto $request)
    {
        $pathFolder = 'storage/avatars';

        $file = $request->file('image');
        $rawFileName = md5_file($file->getRealPath()) . '_' . time();
        $fileName = $rawFileName . '.' . $file->guessExtension();
        $file->move(public_path($pathFolder), $fileName);
        $filePath = '/' . $pathFolder . '/' . $fileName;

        app('user')->update(['image' => '/' . $pathFolder . '/' . $fileName]);

        return response()->json([
            'status' => true,
            'image' => glide($filePath, ['w' => 130, 'h' => 130])
        ]);
    }

    public function save(ProfileData $request, Profile $profile): JsonResponse
    {
        return $profile->save($request);
    }

    public function savePassword(ProfilePassword $request, Profile $profile): RedirectResponse
    {
        $profile->savePassword($request);

        return redirect()->back();
    }

    public function orders(): View
    {
        $orders = app('user')->orders()->latest()->paginate(10);
        $pageName = __t('Замовлення');

        return view('profile.orders', compact('orders', 'pageName'));
    }

    public function favorites(): View
    {
        $favorites = Product::with('status')->paginate(9);

        // $data = (app('user')) ? Product::whereLikedBy(app('user')->id)->selectRaw('SUM(price) as total, COUNT(id) as count, GROUP_CONCAT(id SEPARATOR ",") as ids')->where('is_active',1)->where('quantity','>',0)->first() : collect([]);

        //$favoritesCount = $data->count;
        //$favoritesTotal = $data->total;
        //$favoritesIds = $data->ids;

        //$pageName = __t('Обране');

        return view('profile.favorites', compact('favorites'));
    }

    public function viewedProducts(Product $product): View
    {
        $products = $product->getView();
        $viewedCount = $products ? $products->where('is_active', 1)->where('quantity', '>', 0) : collect();
        $viewedCount = $viewedCount->isEmpty() ? 0 : $viewedCount->count();
        $viewedTotal = $products ? $products->where('quantity', '>', 0)->sum('price') : 0;
        $viewedIds = $products ? $products->where('quantity', '>', 0)->pluck('id')->implode(',') : '';

        $products = $this->paginate($products, 8, null, ['path' => url()->current()]);

        $pageName = __t('Переглянуті товари');

        return view('profile.viewed_products',
            compact('products', 'viewedCount', 'viewedTotal', 'viewedIds', 'pageName'));
    }

    public function cashback(): View
    {
        $pageName = __t('Кешбек');

        $user = app('user');

        return view('profile.cashback', compact('pageName', 'user'));
    }

    public function discount(): View
    {
        $pageName = __t('Скидки');

        $user = app('user');

        $discounts = CumulativeDiscount::orderBy('discount', 'asc')->get();

        return view('profile.discount', compact('pageName', 'user', 'discounts'));
    }

    public function logout(): RedirectResponse
    {
        Sentinel::logout();

        return redirect('/');
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function subscriptions(): View
    {
        $pageName = __t('Підписки');
        return view('profile.subscriptions', compact('pageName'));
    }

    public function reviews(): View
    {
        $pageName = __t('Відгуки');
        return view('profile.reviews', compact('pageName'));
    }

    public function service(): View
    {
        $pageName = __t('SmartMag Cервіс');
        return view('profile.service', compact('pageName'));
    }
}
