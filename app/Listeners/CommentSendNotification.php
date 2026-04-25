<?php

namespace App\Listeners;

use App\Events\CommentCreate;
use App\Mail\Comment;
use Illuminate\Support\Facades\Mail;

class CommentSendNotification
{
    public function handle(CommentCreate $event): void
    {
        if (!empty(settingForMail('email-administratora'))) {
            Mail::to(settingForMail('email-administratora'))->send(new Comment($event->comment));
        }
    }
}
