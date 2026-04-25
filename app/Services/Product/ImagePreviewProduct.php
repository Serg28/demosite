<?php

namespace App\Services\Product;

/**
 * Class ImagePreviewProduct
 *
 * Сервис для удаления и управления превью изображений продуктов.
 *
 * @package App\Services\Product
 */
class ImagePreviewProduct
{
    protected string $rootImagePath;

    /**
     * Создает новый экземпляр сервиса.
     *
     * @param string|null $rootImagePath Путь к корневой директории превью. Если не указан, используется значение по умолчанию.
     */
    public function __construct(?string $rootImagePath = null)
    {
        $this->rootImagePath = $rootImagePath ?: public_path('storage/pictures');
    }

    /**
     * Удаляет превью для указанного продукта.
     *
     * @param int $productId ИД продукта для удаления превью.
     *
     * @return bool True, если операция выполнена успешно, в противном случае - False.
     */
    public function deletePreviewsForProduct(int $productId): bool
    {
        $dir = $this->rootImagePath.'/'.$productId;

        if (is_dir($dir)) {
            $items = glob($dir . '/*');
            if (count($items) > 0) {
                foreach ($items as $item) {
                    if (is_dir($item)) {
                        $this->deleteDirectoryAndContents($item);
                    }
                }
                return true; // Успешное выполнение операции
            }
        } else {
            return false; // Ошибка: директория не существует
        }
    }

    /**
     * Удаляет все превью продуктов.
     *
     * @return bool True, если операция выполнена успешно, в противном случае - False.
     */
    public function deleteAllPreviews(): bool
    {
        if (is_dir($this->rootImagePath)) {
            $items = glob($this->rootImagePath . '/*');
            if (count($items) > 0) {
                foreach ($items as $item) {
                    if (is_dir($item)) {
                        $subItems = glob($item . '/*');
                        if (count($subItems) > 0) {
                            foreach ($subItems as $subItem) {
                                if (is_dir($subItem)) {
                                    $this->deleteDirectoryAndContents($subItem);
                                }
                            }
                        }
                    }
                }
                return true; // Успешное выполнение операции
            }
        } else {
            return false; // Ошибка: директория не существует
        }
    }

    /**
     * Рекурсивно удаляет директорию и ее содержимое.
     *
     * @param string $dir Директория для удаления.
     *
     * @throws \RuntimeException Если не удалось удалить директорию.
     */
    private function deleteDirectoryAndContents(string $dir): void
    {
        $d = opendir($dir);
        while (($entry = readdir($d)) !== false) {
            if ($entry !== '.' && $entry !== '..') {
                if (is_dir($dir . '/' . $entry)) {
                    $this->deleteDirectoryAndContents($dir . '/' . $entry);
                } else {
                    unlink($dir . '/' . $entry);
                }
            }
        }
        closedir($d);
        if (!rmdir($dir)) {
            throw new \RuntimeException("Не удалось удалить директорию $dir.");
        }
    }
}

