<?php

namespace App\Livewire\Profile\User;

use Livewire\Attributes\On;
use Livewire\Component;

class Data extends Component
{
    #[On('user-profile-updated')]
    public function render()
    {
        $user = app('user');

        // Проверка на авторизацию
        if (!$user) {
            abort(403, 'Доступ запрещен');
        }

        return view('livewire.profile.user.data', [
            'user' => $user,
        ]);
    }
}
