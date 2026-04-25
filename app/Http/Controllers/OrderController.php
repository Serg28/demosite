<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductAvaliability;
use App\Mail\AvailabilityOrderMail;
use App\Models\AvailabilityOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Vis\Builder\TreeController;

class OrderController extends TreeController
{
    public function availability(ProductAvaliability $request): JsonResponse
    {
        $order = AvailabilityOrder::create($request->except(['_token', 'g_recaptcha_response']));

        Mail::to(settingForMail('email-administratora'))->send(new AvailabilityOrderMail($order));

        return response()->json(
            [
                'status' => 'success',
                'message' => __t('Дякую. Скоро з Вами зв\'яжуться'),
            ]
        );
    }
}
