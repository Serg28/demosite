<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
//use Illuminate\Contracts\Http\Kernel as HttpKernel;
//use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

trait PerformanceMonitoringTrait
{

    public function bootPerformanceMonitoringTrait(): void
    {
        $this->registerPerformanceMonitoring();
    }

    public function registerPerformanceMonitoring(): void
    {
        // -----------------  Строгий режим моделей  ------------------------
        // см. https://habr.com/ru/articles/794348/
        // Model::shouldBeStrict(); включает сразу 3 режима:
        // -- Предотвращение N+1 (выдает исключение при попытке ленивой загрузки связей)
        //    Model::preventLazyLoading();
        // -- Защита от частично гидрированных моделей (при попытке получить доступ к незагруженному полю модели выдает не null, а исключение)
        //    Model::preventAccessingMissingAttributes();
        // -- Защита от массового присвоения (при попытке присвоить значения запрещенным полям модели выдается исключение вместо тихого игнорирования)
        //    Model::preventSilentlyDiscardingAttributes();
        //Model::shouldBeStrict();

        //Пока включаем только это
        Model::preventLazyLoading();

        if (app()->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);
                info("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }

        if (!app()->isProduction()) {
            //Логгируем запросы длиннее 1 секунды
            \DB::listen(function ($query) {
                if ($query->time > 1000) {
                    Log::warning("An individual database query exceeded 1 second.", [
                        'sql' => $query->sql
                    ]);
                }
            });

            /*
            if ($this->app->runningInConsole()) {
                $this->app[ConsoleKernel::class]->whenCommandLifecycleIsLongerThan(
                    5000,
                    function ($startedAt, $input, $status) {
                        Log::warning("A command took longer than 5 seconds.");
                    }
                );
            } else {
                $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
                    5000,
                    function ($startedAt, $request, $response) {
                        Log::warning("A request took longer than 5 seconds.");
                    }
                );
            }*/
        }
    }
}
