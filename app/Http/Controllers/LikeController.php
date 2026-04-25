<?php
/*
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class LikeController extends BaseController
{

    public function add(Product $product): JsonResponse
    {
        $product->like(app('user')->id);
        return $this->response($product, __t('Товар успешно добавлен в избранное'));
    }

    public function delete(Product $product): JsonResponse
    {
        $product->unlike(app('user')->id);
        return $this->response($product, __t('Товар удален из избранного'));
    }

    private function getData() {
        return (app('user')) ? Product::whereLikedBy(app('user')->id)->selectRaw('SUM(price) as total, COUNT(id) as count, GROUP_CONCAT(id SEPARATOR ",") as ids')->first() : collect([]);
    }

    private function getCount() {
        return (app('user')) ? Product::whereLikedBy(app('user')->id)->count() : '';
    }

    public function getTotal()
    {
        $total = (app('user')) ? Product::whereLikedBy(app('user')->id)->selectRaw('SUM(price) as total')->value('total') : '';
        session()->put('favorite_total', $total);
        return $total;
    }

    public function getInfo() {
        $data = $this->getData();
        return response()->json(
            [
                'status' => 'success',
                'count' => $data->count,
                'total' => $data->total,
                'ids'   => $data->ids
            ]
        );
    }

    private function response(Product $product, string $message): JsonResponse
    {
        $data = $this->getData();
        return response()->json(
            [
                'status' => 'success',
                'count' => $data->count,
                'total' => $data->total,
                'ids'   => $data->ids,
                'message' => $message,
                'product' => $product->getProductDataAnalitic()
            ]
        );
    }
}

*/


namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends BaseController
{

//    public function add(Product $product): JsonResponse
//    {
//        $product->like(app('user')->id);
//        return $this->getResponse($product, __t('Товар успешно добавлен в избранное'));
//    }
//
//    public function delete(Product $product): JsonResponse
//    {
//        $product->unlike(app('user')->id);
//        return $this->getResponse($product, __t('Товар удален из избранного'));
//    }

    public function toggle(Product $product)
    {
        // Проверка, авторизован ли пользователь
        if (!empty(app('user'))) {
            $userId = app('user')->id;
            if ($product->checkLike()) {
                $product->unlike($userId);
                $message = 'Товар видалено з обраного';
            } else {
                $product->like($userId);
                $message = 'Товар успішно додано до обраного';
            }

            return response()->json([
                'message' => __t($message),
                'title' => __t('Вибране'),
                'status' => 'success',
                'isActive' => $product->checkLike(), // Возвращаем новый статус
            ]);
        }

        // Уведомление о том, что нужно авторизоваться
        return response()->json([
            'message' => __t("Потрібно авторизуватися, щоб додавати товари до обраного"),
            'title' => __t('Вибране'),
            'status' => 'warning',
        ]);
    }

    public function add(Product $product): JsonResponse
    {
        // Проверка, авторизован ли пользователь
        if (!empty(app('user'))) {
            // Проверяем, добавлен ли продукт в избранное
            if ($product->checkLike(app('user')->id)) {
                return response()->json([
                    'message' => __t('Товар вже в обраному'),
                    'title' => __t('Вибране'),
                    'status' => 'warning',
                ]);
            }

            $product->like(app('user')->id);
            // Возвращаем ответ с сообщением
            return $this->getResponse($product, __t('Товар успішно додано до обраного'));
        }

        // Уведомление о том, что нужно авторизоваться
        return response()->json([
            'message' => __t("Потрібно авторизуватися, щоб додавати товари до обраного"),
            'title' => __t('Вибране'),
            'status' => 'warning',
        ]);
    }

    public function delete(Product $product): JsonResponse
    {
        // Проверка, авторизован ли пользователь
        if (!empty(app('user'))) {
            // Проверяем, находится ли продукт в избранном
            if (!$product->checkLike(app('user')->id)) {
                return response()->json([
                    'message' => __t('Товар не в обраному'),
                    'title' => __t('Вибране'),
                    'status' => 'warning',
                ]);
            }

            $product->unlike(app('user')->id);
            // Возвращаем ответ с сообщением
            return $this->getResponse($product, __t('Товар видалено з обраного'));
        }

        // Уведомление о том, что нужно авторизоваться
        return response()->json([
            'message' => __t("Потрібно авторизуватися, щоб додавати товари до обраного"),
            'title' => __t('Вибране'),
            'status' => 'warning',
        ]);
    }


    public function getTotal()
    {
        $total = Product::whereLikedBy(app('user')->id)->sum('price');
        session()->put('favorite_total', $total);
        return $total;
    }

    public function getInfo()
    {
        $data = $this->getData();
        return response()->json([
            'status' => 'success',
            'count' => $data->count,
            'total' => $data->total,
            'ids' => $data->ids
        ]);
    }

    private function getData()
    {
        return (app('user'))
            ? Product::whereLikedBy(app('user')->id)->selectRaw('SUM(price) as total, COUNT(id) as count, GROUP_CONCAT(id SEPARATOR ",") as ids')->first()
            : collect([]);
    }

    private function getResponse(Product $product, string $message): JsonResponse
    {
        $data = $this->getData();
        return response()->json([
            'title' => __t('Успешно'),
            'status' => 'success',
            'count' => $data->count,
            'total' => $data->total,
            'ids' => $data->ids,
            'message' => $message,
            'product' => $product->getProductDataAnalitic()
        ]);
    }

    public function status(Request $request)
    {
        $user = app('user');

        if ($user) {
            // Получаем ID продуктов из запроса
            $productIds = $request->input('productIds', []);

            // Получаем список избранных продуктов для текущего пользователя, которые есть в массиве $productIds
            $favorites = Product::whereIn('id', $productIds)
                ->whereLikedBy($user->id)
                ->pluck('id')
                ->toArray();

            // Возвращаем только ID избранных товаров
            return response()->json([
                'favorites' => $favorites
            ]);
        }

        return response()->json([
            'message' => __t("Потрібно авторизуватися, щоб додавати товари до обраного"),
            'title' => __t('Вибране'),
            'status' => 'warning',
        ]);
    }

}
