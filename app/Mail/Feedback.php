<?php

namespace App\Mail;

use App\Models\Feedback as FeedbackModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Feedback extends Mailable
{
    use Queueable;
    use SerializesModels;

    private FeedbackModel $feedback;

    public function __construct(FeedbackModel $feedback)
    {
        $this->feedback = $feedback;
    }

    public function build(): void
    {
        $this
           ->subject(__t("Зворотній зв'язок"))
           ->view('mails.feedback')->with([
               'feedback' => $this->feedback,
           ]);
    }
}
