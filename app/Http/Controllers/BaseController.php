<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    protected function returnError(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => __t($message),
        ]);
    }

    protected function returnSuccess(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => __t($message),
        ]);
    }
}
