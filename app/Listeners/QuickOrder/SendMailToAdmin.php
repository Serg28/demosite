<?php

namespace App\Listeners\QuickOrder;

use App\Events\QuickOrderCreate;
use App\Mail\QuickOrder;
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
    public ?string $queue = 'shop-emails';

    public function handle(QuickOrderCreate $event): void
    {
        if (!empty(settingForMail('email-administratora'))) {
            Mail::to(settingForMail('email-administratora'))->send(new QuickOrder($event->order));
        }
    }
}
