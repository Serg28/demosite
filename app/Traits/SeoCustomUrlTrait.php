<?php

namespace App\Traits;

use App\Models\MorphOne\Seo;
use App\Models\SeoUrls;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

trait SeoCustomUrlTrait
{
    public function seo()
    {
        return $this->getSeoUrl();
    }

    public function getSeoUrl()
    {
        $locale = App::getLocale();
        /*$requestUri = Request::getRequestUri();
        if ($seourl = SeoUrls::where('link->' . $locale, 'like', $requestUri)
            ->orWhere('link->' . $locale, 'like', trim($requestUri, '/'))
            ->orWhere('link->' . $locale, 'like', Request::fullUrl())
            ->orWhere('link->' . $locale, 'like', Request::url())
            ->active()->first()) {
            $this->seo = $seourl->seo;
            return $seourl->seo();
        }*/

        $requestUri = currentUrl();
        $requestUriPath = currentUrlPath();

        return once(function () use ($locale, $requestUri, $requestUriPath) {
            if ($seoUrl = SeoUrls::where('link->' . $locale, 'like', $requestUri)
                ->orWhere('link->' . $locale, 'like', trim($requestUri, '/'))
                ->orWhere('link->' . $locale, 'like', $requestUriPath)
                ->orWhere('link->' . $locale, 'like', '/' . $requestUriPath)
                ->orWhere('link->' . $locale, 'like', trim($requestUriPath, '/'))
                ->active()->first()) {
                $this->seo = $seoUrl->seo;
                return $seoUrl->seo();
            }

            return $this->morphOne(Seo::class, 'seo');
        });
    }
}
