<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class ProductsGenerateNewUrls extends Command
{
    protected $signature = 'products:generate-new-urls';

    protected $description = 'Command description';

    public function handle(): void
    {
        try {
            // Получаем записи категорий, у которых и пустой слаг, и пустой URL
            $categories = Product::where(function ($query) {
                $query->whereNull('slug')->orWhere('slug', '');
            })->where(function ($query) {
                $query->whereNull('url')->orWhere('url', '');
            })->get();

            $updatedCategoriesCount = 0;

            foreach ($categories as $category) {
                // Генерируем slug на основе ua версии title
                $category->slug = $this->generateSlug($category);

                // Если поле url пустое или null, генерируем его на основе title
                //$category->url = $this->generateUrl($category);

                // Обновляем запись в базе данных
                $category->save(); //Здесь можно сохранять без вызова событий или update. Или massUpdate  (см. https://github.com/iksaku/laravel-mass-update)

                $updatedCategoriesCount++;
            }

            $this->info("Successfully updated $updatedCategoriesCount products.");
        } catch (\Exception $e) {
            $this->error('An error occurred while updating products: ' . $e->getMessage());
        }
    }

    private function generateSlug($product): string
    {
        // Получаем slug из товара
        $slug = $product->getSlug().\Str::slug(($product->code ? "-{$product->code}" : ''));

        // Проверяем на уникальность slug, добавляем id только в случае дубля
        return Product::where('slug', $slug)->where('id', '!=',
            $product->id)->exists() ? $slug . '-' . $product->id : $slug;
    }

    private function generateUrl($product): bool|string
    {
        // Декодируем title в массив
        $titleArray = json_decode($product->title, true);
        $urls = [];
        // Проверяем, содержит ли слаг идентификатор
        $idInSlug = str_contains($product->slug, '-' . $product->id) ? '-' . $product->id : '';

        // Проходим по каждой языковой версии и генерируем URL
        foreach ($titleArray as $lang => $langTitle) {
            $slug = $langTitle ? \Str::slug($langTitle) . $idInSlug : '';
            $urls[$lang] = $slug ? $this->checkUrlUniqueness($slug, time(), $lang) : '';
        }

        // Возвращаем JSON сгенерированных URL
        return json_encode($urls);
    }


    // Метод для проверки уникальности URL
    private function checkUrlUniqueness($slug, $id, $lang): string
    {
        // Проверяем на уникальность url в виде JSON и чтобы урл не совпадал с полем слаг других записей, кроме текущей
        $count = Product::where(static function ($query) use ($slug, $id, $lang) {
            $query->where('url->' . $lang, $slug)->orWhere('slug', $slug);
        })->where('id', '!=', $id)->count();

        return ($count > 0) ? $slug . '-' . $id : $slug;
    }
}
