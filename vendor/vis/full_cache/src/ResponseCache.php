<?php

namespace Vis\FullCache;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseCache
{

    /**
     * @var RequestHasher
     */
    protected $hasher;

    /**
     * @var ResponseCache
     */
    protected $cache;

    public function __construct(ResponseCacheRepository $cache, RequestHasher $hasher)
    {
        $this->hasher = $hasher;
        $this->cache = $cache;
    }


    public function shouldCache(Request $request)
    {
        if (!$this->accessCache($request)) {
            return false;
        }

        return true;
    }

    public function cacheResponse(Request $request, Response $response)
    {
        $this->cache->put($this->hasher->getHashFor($request), $response);
    }

    public function hasCached(Request $request)
    {
        if (!$this->accessCache($request)) {
            return false;
        }

        return $this->cache->has($this->hasher->getHashFor($request));
    }

    public function getCachedResponseFor(Request $request)
    {
        return $this->cache->get($this->hasher->getHashFor($request));
    }

    private function accessCache(Request $request)
    {
        if (!config('full_cache.enabled')) {
            return false;
        }

        //if ($request->isMethod('post')) {
        if ($request->isMethod('post') || $request->ajax()) {
            return false;
        }

        if (in_array($request->path(), config('full_cache.notWillCacheUrl'))) {
            return false;
        }

        if (is_array(config('full_cache.cacheUrl')) && count(config('full_cache.cacheUrl'))) {

            if (!in_array($request->path(), config('full_cache.cacheUrl'))) {
                return false;
            }
        }

        return true;
    }

}
