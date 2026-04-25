<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateApi
{
    public function handle($request, Closure $next)
    {

        //Даем пользователю возможность передать токен (api-ключ) разными способами
        //1. в адресе запроса
        //$token = $request->query('api_token');
        //if(empty($token)){
        //2. Через url-form-encoded поля POST запроса
        //    $token = $request->input('api_token');
        //}
        //if(empty($token)){
        //3. Через заголовок Authorization: Bearer ......
        //    $token = $request->bearerToken();
        //}

        $token = $request->bearerToken();
        $ip = $request->ip();

        if ($this->isAllowedAccess($ip, $token)) {
            return $next($request);
        }

        return response()->json([
            'errors' => [
                'status' => 403,
                'message' => 'Forbidden. IP or token not allowed.',
            ],
        ], 403);
    }

    /**
     * Проверяет, разрешен ли текущий IP и токен.
     *
     * @param string $currentIP
     * @param string|null $token
     * @return bool
     */
    private function isAllowedAccess(string $currentIP, ?string $token): bool
    {
        $allowedIPs = $this->getAllowedIPs();
        $apiToken = setting('site-api-bearer-key') ?: config('services.api.token');

        return (
            (empty($allowedIPs) || in_array($currentIP, $allowedIPs)) &&
            ($token === $apiToken)
        );
    }

    /**
     * Получает список разрешенных IP-адресов из настроек.
     *
     * @return array
     */
    private function getAllowedIPs(): array
    {
        $ipList = setting('site-api-allowed-ip');

        return empty($ipList) ? [] : array_map('trim', explode(',', $ipList));
    }
}
