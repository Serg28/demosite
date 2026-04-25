<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Middleware, that adds different headers to response
 */
class RequestCache
{
    const SLEEP_TIME = 200000;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $params = func_get_args();
        $tags = ['response'];
        if (count($params) > 2) {
            $tags = array_merge($tags, array_slice($params, 2));
        }
        $queryParams = $request->query();
        $queryParams['url'] = $request->path();
        ksort($queryParams);
        $paramHash = md5(serialize($queryParams));
        $key = 'responseCache:'.$paramHash;
        $pendingKey = $key.':pending';
        $response = null;
        if ($request->isMethod('get') && ! request()->ajax() && ($response = $this->getCache($key, $pendingKey, $tags)) === null) {
            Cache::tags(array_merge($tags, ['pending']))->put($pendingKey, true, 0.3);
            /** @var Response $response */
            $response = $next($request);
            if ($response->isSuccessful()) {
                try {
                    Cache::tags($tags)->put($key, $response, 5);
                } catch (\Exception $ex) {
                    Log::info($ex->getMessage());
                }
            }
            Cache::tags(array_merge($tags, ['pending']))->forget($pendingKey);
        } elseif ($response === null) {
            $response = $next($request);
        }

        return $response;
    }

    /**
     * Get cached data
     *
     * @param  string  $key
     * @param  string  $pendingKey
     * @param  array  $tags
     * @return Response
     */
    protected function getCache($key, $pendingKey, $tags = [])
    {
        while (Cache::tags(array_merge($tags, ['pending']))->has($pendingKey)) {
            usleep(self::SLEEP_TIME);
        }

        if (Cache::tags($tags)->has($key)) {
            return Cache::tags($tags)->get($key);
        }

        return null;
    }
}
