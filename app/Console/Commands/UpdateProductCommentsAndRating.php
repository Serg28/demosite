<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProductCommentsAndRating extends Command
{
    // Название и описание команды
    protected $signature = 'products:update-comments-rating';
    protected $description = 'Обновить количество комментариев и рейтинг для всех продуктов';

    public function handle()
    {
        // Перебираем все продукты
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                $comments = $product->comments()->active();

                // Считаем количество активных комментариев
                $countComments = $comments->count();

                // Считаем средний рейтинг только для комментариев с рейтингом больше нуля
                $avgComments = $comments->where('rating', '>', 0)->avg('rating');

                // Обновляем данные продукта
                $product->update([
                    'count_comments' => $countComments,
                    'rating' => $avgComments,
                ]);

                // Логируем обновление
                //\Log::info('Product comments and rating updated', [
                //    'product_id' => $product->id,
                //    'count_comments' => $countComments,
                //    'rating' => $avgComments,
                //]);
            }
        });

        $this->info('Обновление комментариев и рейтинга для всех продуктов завершено.');
    }
}