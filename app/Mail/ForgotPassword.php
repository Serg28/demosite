<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable;
    use SerializesModels;

    private User $user;

    private string $newPassword;

    public function __construct(User $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function build(): void
    {
        $this
            ->subject(__t('Нагадування пароля'))
            ->view('mails.forgot_password')->with([
                'user' => $this->user,
                'newPassword' => $this->newPassword,
            ]);
    }
}
