<?php

namespace App\Mail;

use App\Models\Comment as CommentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Comment extends Mailable
{
    use Queueable;
    use SerializesModels;

    private CommentModel $comment;

    public function __construct(CommentModel $comment)
    {
        $this->comment = $comment;
    }

    public function build(): void
    {
        $this
            ->subject(__t('Новый комментарий на сайте'))
            ->view('mails.comment')->with([
                'comment' => $this->comment,
            ]);
    }
}
