<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('blade-cache-directive.php'), 'blade-cache-directive');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('money', function ($money) {
            return "<?php echo number_format({$money}, 0, ',', ' '); ?>";
        });

        Blade::directive('loop', function ($expression) {
            return "<?php foreach ($expression): ?>";
        });

        Blade::directive('endloop', function ($expression) {
            return '<?php endforeach; ?>';
        });

        Blade::if('notLighthouse', function () {
            return stripos(request()->header('User-Agent'), 'Lighthouse') === false;
        });


        /*
        Blade::directive('cache', function ($expression) {
            return "<?php
                \$__cache_directive_arguments = [{$expression}];

                if (count(\$__cache_directive_arguments) === 3) {
                    [\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_tags] = \$__cache_directive_arguments;
                } elseif (count(\$__cache_directive_arguments) === 2) {
                    [\$__cache_directive_key, \$__cache_directive_ttl] = \$__cache_directive_arguments;
                    \$__cache_directive_tags = ['products'];
                } else {
                    [\$__cache_directive_key] = \$__cache_directive_arguments;
                    \$__cache_directive_ttl = config('blade-cache-directive.ttl');
                    \$__cache_directive_tags = ['products'];
                }

                if (\Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags)->has(\$__cache_directive_key)) {
                    echo \Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags)->get(\$__cache_directive_key);
                } else {
                    \$__cache_directive_buffering = true;

                    ob_start();
            ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
                    \$__cache_directive_buffer = ob_get_clean();

                    \Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags)->put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);

                    echo \$__cache_directive_buffer;

                    unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments, \$__cache_directive_tags);
                }
            ?>";
        });*/

        //Добавлен третий параметр - тип кеша. По-умолчанию file

        Blade::directive('cache', function ($expression) {
            return "<?php
        \$__cache_directive_arguments = [{$expression}];

        // Разбор аргументов директивы
        if (count(\$__cache_directive_arguments) === 3) {
            [\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_store] = \$__cache_directive_arguments;
        } elseif (count(\$__cache_directive_arguments) === 2) {
            [\$__cache_directive_key, \$__cache_directive_ttl] = \$__cache_directive_arguments;
            \$__cache_directive_store = 'file'; // Кеш по умолчанию - file
        } elseif (count(\$__cache_directive_arguments) === 1) {
            [\$__cache_directive_key] = \$__cache_directive_arguments;
            \$__cache_directive_ttl = config('blade-cache-directive.ttl', 60); // TTL по умолчанию
            \$__cache_directive_store = 'file';
        }

        // Проверяем, есть ли закешированные данные по ключу
        if (\Illuminate\Support\Facades\Cache::store(\$__cache_directive_store)->has(\$__cache_directive_key)) {
            echo \Illuminate\Support\Facades\Cache::store(\$__cache_directive_store)->get(\$__cache_directive_key);
        } else {
            \$__cache_directive_buffering = true;
            ob_start();
    ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
            \$__cache_directive_buffer = ob_get_clean();

            // Сохраняем в кеш с указанным типом
            \Illuminate\Support\Facades\Cache::store(\$__cache_directive_store)->put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);

            echo \$__cache_directive_buffer;

            unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_store, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments);
            }
        ?>";
        });

    }
}
