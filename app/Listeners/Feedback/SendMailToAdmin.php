<?php

namespace App\Listeners\Feedback;

use App\Events\FeedbackCreate;
use App\Mail\Feedback as FeedbackMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailToAdmin implements ShouldQueue
{
    use SerializesModels;

    /**
     * Имя соединения, на которое должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $connection = 'redis';

    /**
     * Имя очереди, в которую должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $queue = 'low';

    public function handle(FeedbackCreate $event): void
    {
        if (!empty(settingForMail('email-administratora'))) {
            Mail::to(settingForMail('email-administratora'))->send(new FeedbackMail($event->feedback));
        }
    }
}
