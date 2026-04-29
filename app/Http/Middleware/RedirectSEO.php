<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSEO
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();
        $url  = $request->getUri();

        if (
            str_contains($url, 'www.') ||
            str_contains($url, '/index.php') ||
            ($path !== '/' && str_ends_with($url, '/')) ||
            ($request->query('page') == 1)
        ) {
            $newUrl = str_replace(['www.', '/index.php'], '', $url);
            $newUrl = rtrim($newUrl, '/');

            if ($request->query('page') == 1) {
                $parsed = parse_url($newUrl);
                parse_str($parsed['query'] ?? '', $queryParams);
                unset($queryParams['page']);

                $newUrl = $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'];
                if (! empty($queryParams)) {
                    $newUrl .= '?' . http_build_query($queryParams);
                }
            }

            return redirect($newUrl, 301);
        }

        return $next($request);
    }
}
