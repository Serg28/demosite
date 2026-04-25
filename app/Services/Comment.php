<?php

namespace App\Services;

use App\Events\CommentCreate;
use App\Http\Requests\Comment as CommentRequest;
use App\Models\Comment as CommentModel;
use App\Models\Product;
use Illuminate\Support\Collection;

class Comment
{
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function count(): int
    {
        return $this->product->count_comments;
    }

    public function getRating(): float
    {
        return $this->product->rating;
    }

    public function ratingPercent(): float
    {
        return ($this->getRating() / 5) * 100;
    }

    public function all(): Collection
    {
        return $this->product->comments()->active()->rememberForever()
                ->cacheTags($this->cacheTag())->orderBy('created_at', 'desc')->get();
    }

    /*public function add(CommentRequest $request): void
    {

        $user_id = app('user') ? app('user')->id : null;
        $request->merge(['product_id' => $this->product->id, 'user_id' => $user_id]);

        $comment = CommentModel::create($request->except(['g_recaptcha_response']));
        cache()->tags($this->cacheTag())->flush();

        CommentCreate::dispatch($comment);
    }*/
    public function add(CommentRequest $request): bool
    {
        try {
            $user_id = app('user') ? app('user')->id : null;
            $request->merge(['product_id' => $this->product->id, 'user_id' => $user_id]);

            $comment = CommentModel::create($request->except(['g_recaptcha_response']));
            cache()->tags($this->cacheTag())->flush();

            CommentCreate::dispatch($comment);
            return true;
        } catch (\Exception $e) {
            // Обработка ошибки
            return false;
        }
    }


    private function cacheTag(): array
    {
        return ['comment_'.$this->product->id, 'comments'];
    }
}
