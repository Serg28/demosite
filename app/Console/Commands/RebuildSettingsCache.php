<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

/**
 * Class RebuildSettingsCache
 *
 * Класс для очистки и перестроения кэша настроек.
 *
 * @package App\Console\Commands
 */
class RebuildSettingsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:rebuild_setting_cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing and rebuilding the settings cache';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {

            // Проверить, является ли значение настройки пустым
            if (empty($settingValue = $setting->getValue($setting->slug))) {
                continue;
            }

            // Сгенерировать ключ кэша для настройки
            $cacheKey = $setting->slug . App::getLocale();

            // Кэшировать значение настройки
            Cache::tags('settings')->rememberForever($cacheKey, function () use ($settingValue) {
                return $settingValue ?? '';
            });
        }

        return Command::SUCCESS;
    }
}
