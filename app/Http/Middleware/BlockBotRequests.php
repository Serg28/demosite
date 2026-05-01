<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Блокує запити від відомих ботів на службових маршрутах.
 *
 * Застосовувати тільки на службових ендпоінтах (/livewire/update, AJAX),
 * які не потрібні для індексації — зменшує навантаження від ботів.
 * На звичайних SEO-сторінках НЕ застосовувати.
 */
class BlockBotRequests
{
    protected array $botPatterns = [
        'Googlebot', 'Bingbot', 'YandexBot', 'DuckDuckBot', 'Baiduspider',
        'Slurp', 'Sogou', 'SeznamBot', 'Applebot',

        // SEO-краулери
        'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'SEOkicks',
        'Exabot', 'LinkpadBot',

        // AI-боти
        'GPTBot', 'GoogleOther', 'Bytespider', 'PetalBot', 'Amazonbot', 'ClaudeBot',

        // Соцмережі
        'Twitterbot', 'LinkedInBot', 'FacebookExternalHit', 'facebot',
        'Pinterestbot', 'vkShare', 'Quora Link Preview',

        // Підозрілі
        'python',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        $userAgent = $request->header('User-Agent', '');

        foreach ($this->botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return response('', 204);
            }
        }

        return $next($request);
    }
}
