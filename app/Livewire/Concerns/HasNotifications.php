<?php

namespace App\Livewire\Concerns;

/**
 * Трейт для відправки toast-сповіщень з Livewire-компонентів.
 *
 * Диспетчеризує подію `notify`, яку перехоплює Alpine.js компонент `toast`
 * у resources/js/components/toast.js (через @notify.window).
 *
 * @example
 *   use HasNotifications;
 *   // ...
 *   $this->notify('Товар додано в кошик', 'Успіх', 'success');
 *   $this->notify('Помилка підключення', '', 'error');
 */
trait HasNotifications
{
    public function notify(string $message, string $title = '', string $type = 'success'): void
    {
        $this->dispatch('notify', message: $message, title: $title, type: $type);
    }

    public function notifySuccess(string $message, string $title = ''): void
    {
        $this->notify($message, $title, 'success');
    }

    public function notifyError(string $message, string $title = ''): void
    {
        $this->notify($message, $title, 'error');
    }

    public function notifyInfo(string $message, string $title = ''): void
    {
        $this->notify($message, $title, 'info');
    }

    public function notifyWarning(string $message, string $title = ''): void
    {
        $this->notify($message, $title, 'warning');
    }
}
