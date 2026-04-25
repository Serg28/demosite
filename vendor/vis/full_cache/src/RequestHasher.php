<?php

namespace Vis\FullCache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Request2;

class RequestHasher
{

    /**
     * Get a hash value for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function getHashFor(Request $request)
    {
        $keyArray = [
            $request->fullUrl(),
            $request->getMethod(),
            $this->isAjax(),
            $this->getSessionSerialize($request),
            $this->getCookiesSerialize($request)
        ];

        return 'full-cache-'. md5(
            implode("/", $keyArray)
        );
    }

    private function isAjax()
    {
        return Request2::ajax() ? 'ajax' : 'no_ajax';
    }
    
    private function getSessionSerialize(Request $request)
    {
        $sessionsAll[] = $request->session()->get(config('cartalyst.sentinel.session'));

        if (config('full_cache.sessionForCache') && is_array(config('full_cache.sessionForCache'))) {
            foreach (config('full_cache.sessionForCache') as $sessionKey) {
                $sessionsAll[] = $request->session()->get($sessionKey);
            }
        }

        return serialize($sessionsAll);
    }

    private function getCookiesSerialize(Request $request)
    {
        $cookiesAll[] = $request->cookie('skin');

        if (config('full_cache.cookiesForCache') && is_array(config('full_cache.cookiesForCache'))) {
            foreach (config('full_cache.cookiesForCache') as $cookiesKey) {
                $cookiesAll[] = $request->cookie($cookiesKey);
            }
        }

        return serialize($cookiesAll);
    }
}
