<?php

namespace App\Http\Controllers;

//TODO: Здесь регистрация дисконтной карты

//1. По коду и баркоду ищем карту и заносим в нее данные.

use App\Http\Requests\DiscountCardRequest;
use App\Models\DiscountCard;

class DiscountCardController extends BaseController
{
    public function register(DiscountCardRequest $request)
    {
        $code = ltrim($request->post('code'), 0);
        $barcode = ltrim($request->post('barcode'), 0);

        $form = request()->only('name', 'second_name', 'patronymic_name', 'phone', 'email', 'city');

        if ($discouncard = DiscountCard::where([
            'code' => $code,
            'barcode' => $barcode,
            'phone' => '',
            'email' => '',
        ])->first()) {
            if (
                ! $res = $discouncard->update([
                    'name' => implode(' ', [$form['second_name'], $form['name'], $form['patronymic_name']]),
                    'phone' => $form['phone'],
                    'email' => $form['email'],
                    'city' => $form['city'],
                    'is_active' => 1,
                    'waiting_1c' => 1,
                    'regdate' => date('Y-m-d'),
                ])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __t('Техническая ошибка во время регистрации. Попробуйте немного позже'),
                    'html' => '',
                ]);
            }
            $request->session()->put('code', $code);

            return response()->json([
                'success' => true,
                'message' => __t('Дисконтная карта успешно зарегистрирована'),
                'url' => route('discountcard.registered.success'),
                'html' => '',
            ]);
            //return redirect()->route('discountcard.registered.success', ['code' => $code]);
        }
        //return redirect()->route('discountcard.registered.success');
        return response()->json([
            'success' => false,
            'message' => __t('Дисконтная карта с указанными данными не найдена'),
        ]);
    }

    public function success($code = '')
    {
        //dd(request()->session()->get('code'));
        if (request()->session()->get('code')) {
            request()->session()->remove('code');

            return view('discountcard.complete');
        }
        abort(404);
    }
}
