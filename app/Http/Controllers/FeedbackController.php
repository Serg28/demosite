<?php

namespace App\Http\Controllers;

use App\Events\FeedbackCreate;
use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

class FeedbackController extends BaseController
{
    public function send(FeedbackRequest $request): JsonResponse
    {
        $feedback = Feedback::create($request->all());
        FeedbackCreate::dispatch($feedback);

        return $this->returnSuccess('Дякую. Скоро з Вами зв\'яжуться');
    }
}
