<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use MarcinOrlowski\ResponseBuilder\ExceptionHandlerHelper;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e)
    {
        if ($e instanceof ThrottleRequestsException) {
            return response()->json([
                'status' => 'error',
                'title' => __t('Увага'),
                'message' => __t('Забагато запитів. Будь ласка, спробуйте знову за хвилину.')
            ], 429);
        }

        return parent::render($request, $e);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        //Для пакета ResponseBuilder
        $this->renderable(function (Throwable $ex, $request) {
            if ($request->is('*/api/*')) {
                return ExceptionHandlerHelper::render($request, $ex);
            }
        });
        //---
    }
}
