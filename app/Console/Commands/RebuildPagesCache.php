<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Class RebuildPagesCache
 *
 * Класс для очистки и перестроения кэша страниц по указанным роутам.
 *
 * @package App\Console\Commands
 */
class RebuildPagesCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:rebuild_pages_cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear and rebuild the pages cache for specified routes.';

    /**
     * The URLs to clear and rebuild cache for.
     *
     * @var array
     */
    protected array $urls = [
        '/',
        // Добавьте здесь другие URL-ы, которые вы хотите очистить и перестроить.
    ];

    /**
     * The routes for which the cache should be cleared and rebuilt.
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * The list of supported language codes.
     *
     */
    protected $languages = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->languages = getSiteLanguages();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        foreach ($this->languages as $lang) {
            foreach ($this->urls as $url) {

                $fullUrl = getUrl($url, $lang);

                try {
                    Http::get($fullUrl);
                    $this->info('Cache for ' . $fullUrl . ' cleared and rebuilt successfully.');
                } catch (\Exception $e) {
                    $this->error('Failed to clear and rebuild cache for ' . $fullUrl . ': ' . $e->getMessage());
                }
            }
        }

        return Command::SUCCESS;
    }
}
