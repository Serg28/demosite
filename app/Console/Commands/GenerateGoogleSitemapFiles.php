<?php

namespace App\Console\Commands;

use App\Services\Sitemap\Google;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Vis\Builder\Models\Language;

class GenerateGoogleSitemapFiles extends Command
{

    private string $dir = '/sitemap/google/';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:google_to_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all Google Sitemaps into files';

    /**
     * Create a new command instance.
     *
     * @return int
     */

    public function handle(): int
    {
        ini_set('memory_limit','2048M');
        Log::info('---Start Command GenerateGoogleSitemapFiles---');

        $sitemap = new Google();

        $sitemap->cleanDirectory();

        $this->error('Clean directory ' . $sitemap->directory);

        $languages = (new Language())->getLanguages()->pluck('language');

        foreach ($languages as $lang) {

            LaravelLocalization::setLocale($lang);

            $sitemaps = [];
            $sitemap_file_num = 0;
            foreach ($sitemap->getModels() as $model) {
                $items = app($model['model'])->active()->cursor()->chunk($sitemap->batchSize);
                $modelData = $model;
                $modelData['items'] = $items;

                foreach ($modelData['items'] as $urls) {
                    // Одна карта сайта
                    $sitemap_path = $this->dir . $lang . '-' . $modelData['name'] . $sitemap_file_num . '.xml';
                    $sitemap_name = $sitemap->directoryPath . $lang . '-' . $modelData['name'] . $sitemap_file_num . '.xml';

                    if ($output = view(
                        'sitemap.partials.google_urlset',
                        compact('model', 'urls', 'model', 'lang')
                    )) {
                        File::put($sitemap_name, $output);
                        $sitemaps[] = [
                            'loc' => request()->getSchemeAndHttpHost() . $sitemap_path,
                            'lastmod' => Carbon::now()->format('Y-m-d\TH:i:sP')
                        ];
                        $this->info('Sitemap ' . $sitemap_name . ' created successfully');
                    } else {
                        $this->error('Sitemap ' . $sitemap_name . ' not created');
                    }
                    $sitemap_file_num++;
                }

                if ($output = view(
                    'sitemap.google_sitemap_list',
                    ['sitemaps' => $sitemaps, 'model' => $model]
                )
                ) {
                    $sitemap_file = $sitemap->directoryPath . $lang . '-sitemap.xml';
                    File::put($sitemap_file, $output);
                    $this->info('Sitemap list ' . $sitemap_file . ' created successfully');
                } else {
                    $this->error('Sitemap list ' . $sitemap_file . ' not created');
                }
                unset($output);
            }
        }

        Log::info('---Finish Command GenerateGoogleSitemapFiles---');

        return Command::SUCCESS;
    }

}
