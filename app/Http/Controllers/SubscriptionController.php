<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends BaseController
{
    public function send(SubscriptionRequest $request): JsonResponse
    {
        Subscription::create($request->all());

        return $this->returnSuccess('Ви успішно підписані');
    }
}
