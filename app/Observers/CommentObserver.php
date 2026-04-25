<?php

namespace App\Observers;

//use App\Jobs\SendMailToUserCommentAnswerJob;
use App\Models\Comment;

class CommentObserver
{
    public function updated(Comment $comment): void
    {
        // Получаем модель, к которой привязан комментарий
        $parentModel = $comment->commentable;

        // Проверяем, является ли родительская модель продуктом
        if ($parentModel instanceof \App\Models\Product) {
            $comments = $parentModel->comments()->active();
            $countComments = $comments->count();
            // Получаем средний рейтинг только для комментариев с рейтингом больше нуля
            $avgComments = $comments->where('rating', '>', 0)->avg('rating');


            $parentModel->update([
                'count_comments' => $countComments ?? 0,
                'rating' => $avgComments ?? 0,
            ]);
        }
        //\Log::info('Comment updated', ['comment_id' => $comment->id, 'model_id' => $parentModel->id, 'data' => [
        //    'count_comments' => $countComments ?? 0,
        //    'rating' => $avgComments ?? 0,
        //]]);
        // Проверяем наличие ответа на комментарий и факт изменения ответа
        //if (!empty($comment->answer) && $comment->isDirty('answer')) {
        //    SendMailToUserCommentAnswerJob::dispatch($comment)->onQueue('shop-emails');
        //}
    }
}
