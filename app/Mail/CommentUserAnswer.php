<?php

namespace App\Mail;

use App\Models\Comment as CommentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommentUserAnswer extends Mailable
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
            ->subject(__t('Відповідь на коментар на сайті ') . ' ' .ucfirst(env('APP_NAME')))
            ->view('mails.comment_user_answer')->with([
                'comment' => $this->comment,
            ]);
    }
}
