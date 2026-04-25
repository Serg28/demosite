<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Product\ImagePreviewProduct;
use Illuminate\Support\Facades\Artisan;

/**
 * Class RemoveProductPreviews
 *
 * Класс удаления превью товаров. Указав productId, удаляются только для указанного товара. Иначе - для всех.
 *
 * @package App\Console\Commands
 */
class RemoveProductPreviews extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     *
     * @var string
     */
    protected $signature = 'products:remove_product_previews {productId?}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Удаление превью товаров. Указав productId, удаляются только для указанного товара. Иначе - для всех';

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
     * Выполнение консольной команды.
     *
     * @return int
     */
    public function handle(): int
    {
        $productId = $this->argument('productId');

        $message = 'Сканирование всех директорий с превью. Это может занять некоторое время...';

        if ($productId) {
            $message = 'Сканирование директории товара с ID ' . $productId;
        }

        $this->info($message);

        if ($productId) {
            $result = $this->previewProductService->deletePreviewsForProduct($productId);
        } else {
            $result = $this->previewProductService->deleteAllPreviews();
        }

        if ($result) {
            $this->info('Превью успешно удалены.');
        } else {
            $this->error('Произошла ошибка при удалении превью.');
        }

        Artisan::call('cache:clear_rebuild');

        return $result ? Command::SUCCESS : Command::FAILURE;
    }
}
