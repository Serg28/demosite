<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\News;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Tree;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Console\Kernel;

abstract class AbstractSitemapXml
{
    public string $directory;
    public string $directoryPath;

    public int $batchSize = 10000;

    public function __construct()
    {
        $this->directory = '/sitemap/' . mb_strtolower(class_basename($this)) . '/';
        $this->directoryPath = public_path($this->directory);
    }

    public function setBasePath()
    {
        $app = app();
        $request = request()->create('https://site.com/trst');
        $app->instance('request', $request);
        $app->make(Kernel::class)->bootstrap();
    }

    public function getLinks(): array
    {
        $data = [];
        foreach ($this->getModels() as $model) {
            $items = app($model['model'])->active()->seoIndex()->get();
            $data[$model['model']] = $model;
            $data[$model['model']]['items'] = $items;
        }

        return $data;
    }

    public function renderToFile()
    {
        //
        return '';
    }

    /**
     * @return void
     */
    public function cleanDirectory(): void
    {
        File::ensureDirectoryExists($this->directoryPath);
        File::cleanDirectory($this->directoryPath);
    }

    public function getModels(): array
    {
        return [
            [
                'model' => Tree::class,
                'name' => 'document',
                'priority' => '1',
                'changefreq' => 'daily',
            ],
            [
                'model' => Product::class,
                'name' => 'product',
                'priority' => '1',
                'changefreq' => 'daily',
            ],
            [
                'model' => News::class,
                'name' => 'news',
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ],
            [
                'model' => Promotion::class,
                'name' => 'promotion',
                'priority' => '0.8',
                'changefreq' => 'daily',
            ],
            /*[
                'model' => Brand::class,
                'name' => 'brand',
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ],*/
        ];
    }
}
