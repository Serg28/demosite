<?php

namespace App\Http\Middleware;

use Closure;

class RedirectSEO
{
    public function handle($request, Closure $next)
    {
        $path = $request->getPathInfo();
        $url = $request->getUri();

        // Проверяем условия для редиректа
        if (
            str_contains($url, "www.") ||
            str_contains($url, "/index.php") ||
            ($path !== "/" && str_ends_with($url, "/")) ||
            ($request->query('page') == 1)
        ) {
            // Убираем "www." и "/index.php" из URL
            $newUrl = str_replace(["www.", "/index.php"], "", $url);

            // Убираем завершающий слеш, если он не в корне
            $newUrl = rtrim($newUrl, "/");

            // Убираем параметр ?page=1
            if ($request->query('page') == 1) {
                $parsedUrl = parse_url($newUrl);
                parse_str($parsedUrl['query'] ?? '', $queryParams);

                // Удаляем параметр `page`
                unset($queryParams['page']);

                // Собираем новый URL без ?page=1
                $newUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
                if (!empty($queryParams)) {
                    $newUrl .= '?' . http_build_query($queryParams);
                }
            }

            return redirect($newUrl, 301);
        }

        return $next($request);
    }
}
