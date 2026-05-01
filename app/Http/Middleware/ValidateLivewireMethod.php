<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Захист Livewire 3 від RCE-вразливостей.
 * Фікс CVE для nested property hydration та ін'єкції методів.
 */
class ValidateLivewireMethod
{
    protected const FORBIDDEN_METHODS = [
        '__construct', '__destruct', '__call', '__callstatic', '__get', '__set', '__isset',
        '__unset', '__sleep', '__wakeup', '__tostring', '__invoke', '__clone',
        'eval', 'exec', 'system', 'passthru', 'shell_exec', 'assert', 'create_function',
    ];

    protected const ALLOWED_INTERNAL_METHODS = [
        '__lazyLoad',
        '__dispatch',
        '__dispatchSelf',
        '__dispatchTo',
    ];

    protected const METHOD_REGEX = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

    protected const MAX_NESTING_DEPTH = 5;

    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->is('livewire/update') || ! $request->isMethod('post')) {
            return $next($request);
        }

        if (! $request->isJson()) {
            return $next($request);
        }

        $json = json_decode($request->getContent(), true);

        if (! is_array($json) || ! isset($json['components']) || ! is_array($json['components'])) {
            return $this->block($request, 'invalid_structure');
        }

        foreach ($json['components'] as $component) {
            if (! isset($component['snapshot'], $component['updates'], $component['calls'])) {
                return $this->block($request, 'missing_keys');
            }

            if (! is_array($component['updates']) || ! is_array($component['calls'])) {
                return $this->block($request, 'invalid_types');
            }

            if (is_string($component['snapshot'])) {
                if ($snapshot = base64_decode($component['snapshot'], true)) {
                    if ($this->containsSuspiciousPayload($snapshot)) {
                        return $this->block($request, 'suspicious_snapshot');
                    }
                }
            }

            foreach ($component['updates'] as $path => $value) {
                if ($this->isNestedPropertyExploit($path, $value)) {
                    return $this->block($request, 'nested_property_exploit');
                }

                if ($this->exceedsNestingDepth($value, 0)) {
                    return $this->block($request, 'excessive_nesting');
                }

                if ($this->isMaliciousSynthPayload($value)) {
                    return $this->block($request, 'malicious_synth');
                }
            }

            foreach ($component['calls'] as $call) {
                if (! isset($call['method'])) {
                    continue;
                }

                $method = $call['method'];

                if (! preg_match(self::METHOD_REGEX, $method)) {
                    return $this->reject($request, $method, 'invalid_format');
                }

                if ($method[0] === '_' && ! in_array($method, self::ALLOWED_INTERNAL_METHODS, true)) {
                    return $this->reject($request, $method, 'private_or_magic');
                }

                if (in_array(strtolower($method), self::FORBIDDEN_METHODS, true)) {
                    return $this->reject($request, $method, 'forbidden_method');
                }
            }
        }

        return $next($request);
    }

    protected function isNestedPropertyExploit(string $path, mixed $value): bool
    {
        if (is_array($value) && isset($value['s'])) {
            $meta = $value;

            if (isset($meta[0]) && $meta[0] === '__rm__' && str_contains($path, '.')) {
                return true;
            }

            if (isset($meta['s']) && $this->isDangerousSynthType($meta['s'])) {
                return true;
            }
        }

        return false;
    }

    protected function isDangerousSynthType(string $type): bool
    {
        $dangerous = ['callable', 'closure', 'object', 'resource', 'eval', 'exec', 'system', 'shell'];

        return in_array(strtolower($type), $dangerous, true);
    }

    protected function isMaliciousSynthPayload(mixed $value): bool
    {
        if (! is_array($value)) {
            return false;
        }

        if (isset($value['s'])) {
            if (isset($value['class']) && $this->isExecutableClass($value['class'])) {
                return true;
            }

            if (isset($value[0]) && is_string($value[0])) {
                if (preg_match('/O:\d+:|C:\d+:|Serializable/', $value[0])) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function isExecutableClass(?string $class): bool
    {
        if ($class === null) {
            return false;
        }

        $dangerous = [
            'ReflectionClass', 'ReflectionFunction', 'ReflectionMethod',
            'Closure', 'SplFileObject', 'DirectoryIterator',
            'PDO', 'PDOStatement', 'Monolog', 'Symfony',
        ];

        foreach ($dangerous as $pattern) {
            if (stripos($class, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function containsSuspiciousPayload(string $snapshot): bool
    {
        if (preg_match('/O:\d+:"|C:\d+:"|__PHP_Incomplete_Class/i', $snapshot)) {
            return true;
        }

        if (preg_match('/Monolog|Symfony\\\\|Illuminate\\\\|Faker\\\\|Mockery/i', $snapshot)) {
            return true;
        }

        if (preg_match('/eval\(|exec\(|system\(|passthru\(|shell_exec\(/i', $snapshot)) {
            return true;
        }

        return false;
    }

    protected function exceedsNestingDepth(mixed $value, int $depth): bool
    {
        if (! is_array($value)) {
            return false;
        }

        if ($depth >= self::MAX_NESTING_DEPTH) {
            return true;
        }

        foreach ($value as $v) {
            if ($this->exceedsNestingDepth($v, $depth + 1)) {
                return true;
            }
        }

        return false;
    }

    protected function reject(Request $request, string $method, string $reason): mixed
    {
        Log::channel('security')->warning('Livewire method injection blocked', [
            'reason' => $reason,
            'method' => $method,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return response()->json(['message' => 'Method not allowed'], 400);
    }

    protected function block(Request $request, string $reason): mixed
    {
        Log::channel('security')->warning('Livewire exploit blocked', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
