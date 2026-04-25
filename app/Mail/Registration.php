<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Registration extends Mailable
{
    use Queueable;
    use SerializesModels;

    private User $user;

    private string $password;

    private string $activationUrl;

    public function __construct(User $user, string $password, string $activationUrl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->activationUrl = $activationUrl;
    }

    public function build(): void
    {
        $this
            ->subject(__t('Реєстрація'))
            ->view('mails.registration')->with([
                'user' => $this->user,
                'password' => $this->password,
                'activationUrl' => $this->activationUrl,
            ]);
    }
}
