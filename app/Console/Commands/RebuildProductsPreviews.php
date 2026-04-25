<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\Product\ImagePreviewProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Opis\Closure\SerializableClosure;
use Spatie\Async\Pool;
use VXM\Async\AsyncFacade as Async;

class RebuildProductsPreviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:rebuild_previews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning and re-creating previews of products for which the pictures_updated field has a value of 1';

    /**
     * Сервис для работы с превью товаров
     *
     * @var ImagePreviewProduct
     */
    protected ImagePreviewProduct $previewProductService;


    public function __construct(ImagePreviewProduct $previewProductService)
    {
        parent::__construct();
        $this->previewProductService = $previewProductService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $products = Product::picturesUpdated()->select(['id','picture','other_pictures'])->get();

        foreach ($products as $product) {
            $this->generatePreview($product);
        }

        Artisan::call('cache:clear_rebuild');

        return Command::SUCCESS;
    }

    private function generatePreview($product): void
    {
        if($product) {
            $this->previewProductService->deletePreviewsForProduct($product->id);
            $product->getImgPath('', 168);
            $product->getImgPath('', 286);
            $product->getImgPath(633, 500);
            $product->getOtherImgWithOriginal('other_pictures', ['w' => 100, 'h' => 100]);
            return;
        }
    }

}
