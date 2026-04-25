<?php

namespace App\Jobs;

use App\Mail\CommentUserAnswer;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailToUserCommentAnswerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Comment $comment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->onQueue('shop-emails');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Job SendMailToUserCommentAnswerJob started');
        if (isset($this->comment->user) && !empty($this->comment->user->email)) {
            Log::info('Job SendMailToUserCommentAnswerJob send to email: ' . $this->comment->user->email);
            try {
                Mail::to($this->comment->user->email)->send(new CommentUserAnswer($this->comment));
                Log::info('--Mail has been sent successfully.');
            } catch (\Exception $e) {
                Log::error('--Failed to send email: ' . $e->getMessage());
            }
        } else {
            Log::warning('Job SendMailToUserCommentAnswerJob not sent, because email is empty');
        }
        Log::info('Job SendMailToUserCommentAnswerJob finished');
    }
}
