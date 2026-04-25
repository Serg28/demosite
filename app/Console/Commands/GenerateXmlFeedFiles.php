<?php

namespace App\Console\Commands;

use App\Models\Feed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Vis\Builder\Models\Language;

class GenerateXmlFeedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:feeds_to_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all Feeds into files';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    private string $path = 'App\\Services\\Xml\\';

    public function handle()
    {
        Log::info('---Start CRON GenerateXmlFeedFiles---');
        File::cleanDirectory(public_path() . '/xml-feed');
        $this->error('Clean directory ' . public_path() . '/xml-feed');
        $feeds = Feed::query()->active()->get();

        $languages = (new Language())->getLanguages()->pluck('language');

        foreach ($languages as $lang) {
            LaravelLocalization::setLocale($lang);

            foreach ($feeds as $feed) {
                $classXml = $this->path . ucfirst(Str::camel($feed->feed_name));

                if (!class_exists($classXml)) {
                    $this->error($classXml . ' class not found');
                } else {
                    if ($output = (new $classXml())->renderToFile($feed)) {
                        File::put(public_path() . '/xml-feed/'.$lang .'-' . $feed->feed_name . '-' . $feed->id . '.xml', $output);
                        $this->info('Feed ' . $lang .'-'.$feed->feed_name . '-' . $feed->id . '.xml created successfully');
                    } else {
                        $this->error('Feed ' . $lang .'-'.$feed->feed_name . '-' . $feed->id . '.xml not created');
                    }
                }
            }

        }
        Log::info('---Finish CRON GenerateXmlFeedFiles---');

        return Command::SUCCESS;
    }
}
