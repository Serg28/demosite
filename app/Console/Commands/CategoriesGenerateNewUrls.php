<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class CategoriesGenerateNewUrls extends Command
{
    protected $signature = 'categories:generate-new-urls';

    protected $description = 'Command description';

    public function handle(): void
    {
        try {
            // Получаем записи категорий, у которых и пустой слаг, и пустой URL
            $categories = Category::where(function ($query) {
                $query->whereNull('slug')->orWhere('slug', '');
            })->where(function ($query) {
                $query->whereNull('url')->orWhere('url', '');
            })->get();

            $updatedCategoriesCount = 0;

            foreach ($categories as $category) {
                // Генерируем slug на основе ua версии title
                $category->slug = $this->generateSlug($category);

                // Если поле url пустое или null, генерируем его на основе title
                $category->url = $this->generateUrl($category);

                // Обновляем запись в базе данных
                $category->saveQuietly(); //Здесь можно сохранять без вызова событий или update. Или massUpdate  (см. https://github.com/iksaku/laravel-mass-update)

                $updatedCategoriesCount++;
            }

            $this->info("Successfully updated $updatedCategoriesCount categories.");
        } catch (\Exception $e) {
            $this->error('An error occurred while updating categories: ' . $e->getMessage());
        }
    }

    private function generateSlug($category): string
    {
        // Получаем slug из категории
        $slug = $category->getSlug();

        // Проверяем на уникальность slug, добавляем id только в случае дубля
        return Category::where('slug', $slug)
            ->where('id', '!=', $category->id)->exists() ? $slug . '-' . $category->id : $slug;
    }

    private function generateUrl($category): bool|string
    {
        // Декодируем title в массив
        $titleArray = json_decode($category->title, true);
        $urls = [];
        // Проверяем, содержит ли слаг идентификатор
        $idInSlug = str_contains($category->slug, '-' . $category->id) ? '-' . $category->id : '';

        // Проходим по каждой языковой версии и генерируем URL
        foreach ($titleArray as $lang => $langTitle) {
            $slug = $langTitle ? \Str::slug($langTitle) . $idInSlug : '';
            $urls[$lang] = $slug ? $this->checkUrlUniqueness($slug, $category->id, $lang) : '';
        }

        // Возвращаем JSON сгенерированных URL
        return json_encode($urls);
    }


    // Метод для проверки уникальности URL
    private function checkUrlUniqueness($slug, $id, $lang): string
    {
        // Проверяем на уникальность url в виде JSON и чтобы урл не совпадал с полем слаг других записей, кроме текущей
        $count = Category::where(static function ($query) use ($slug, $lang) {
            $query->where('url->' . $lang, $slug)->orWhere('slug', $slug);
        })->where('id', '!=', $id)->count();

        return ($count > 0) ? $slug . '-' . ($count+1) : $slug;
    }
}
