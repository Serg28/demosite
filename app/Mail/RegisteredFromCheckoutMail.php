<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisteredFromCheckoutMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __t('Ваш акаунт створено'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.registered-from-checkout',
            with: [
                'resetUrl' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $this->user->email,
                ], false)),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
