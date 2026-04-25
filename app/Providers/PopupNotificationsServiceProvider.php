<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Component;

class PopupNotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        Component::macro('notify', function ($message, $title = '', $type = 'success') {
            $this->dispatch('notify', message: $message, title: $title, type: $type);
        });
    }
}
