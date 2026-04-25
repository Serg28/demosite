<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Comment;

class CopyOldReviews extends Command
{
    protected $signature = 'copy:old_reviews';
    protected $description = 'Копировать старые отзывы из временной таблицы в основную таблицу отзывов';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $treeCommentableId = 41;

        DB::table('reviews')->truncate();
        $oldReviews = DB::table('reviews_old')->get();

        foreach ($oldReviews as $oldReview) {
            $commentableId = null;

            if ($oldReview->commentable_type === 'product') {
                if (empty($oldReview->article)) {
                    $this->warn("Пропуск отзыва без артикула для продукта: {$oldReview->name}");
                    continue;
                }

                $product = Product::where('id_1c', $oldReview->article)->first(['id']);
                if ($product) {
                    $commentableId = $product->id;
                } else {
                    $this->warn("Продукт не найден для артикула: {$oldReview->article}");
                    $oldReview->commentable_type = 'tree';
                    $oldReview->commentable_id = $treeCommentableId;
                    continue;
                }
            } elseif ($oldReview->commentable_type === 'tree') {
                $commentableId = $treeCommentableId;
            }

            $name = preg_replace('/\s+/', ' ', trim($oldReview->name));
            $body = preg_replace('/\s+/', ' ', trim($oldReview->body));

            Comment::create([
                'name' => $name,
                'body' => $body,
                'commentable_type' => $oldReview->commentable_type === 'tree' ? 'App\Models\Tree' : $oldReview->commentable_type,
                'commentable_id' => $commentableId,
                'created_at' => $oldReview->created_at,
                'updated_at' => $oldReview->created_at,
                'is_active' => 1,
            ]);

            $this->info("Отзыв скопирован: {$name}");
        }

        $this->info('Все отзывы успешно скопированы.');
    }
}
