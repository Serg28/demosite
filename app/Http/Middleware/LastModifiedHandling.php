<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Facades\LastModified;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class LastModifiedHandling
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->ajax()) {
            $response = $next($request);
            $response->headers->remove('Last-Modified');
            $response->headers->remove('ETag');
            $response->setExpires(new \DateTime());
            $response->setPublic();
            $response->setMaxAge(0);
            return $response;
        }

        $response = $next($request);

        if (!config('last-modified.enable')) {
            return $response;
        }

        if (is_null(LastModified::get())) {
            return $response;
        }

        if (!in_array(strtoupper($request->getMethod()), ['GET', 'HEAD'])) {
            return $response;
        }

        if ($response instanceof Response) {
            $response->header('Last-Modified', LastModified::get()->toRfc7231String());
        }

        $requestDateTimeString = request()->header('If-Modified-Since');
        if (!is_string($requestDateTimeString)) {
            return $response;
        }

        if (LastModified::get()->lessThanOrEqualTo(Carbon::createFromTimeString($requestDateTimeString, 'GMT'))) {
            abort(304);
        }

        return $response;
    }
}
