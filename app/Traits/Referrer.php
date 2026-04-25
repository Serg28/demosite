<?php

namespace App\Traits;

use Livewire\Attributes\Locked;

/**
 * Трейт Referrer
 *
 * Этот трейт предоставляет метод для получения referrer в зависимости от определенных условий.
 * Если текущий URL содержит 'livewire/update', возвращается referrer, в противном случае возвращается текущий URL.
 * После добавления трейта в компонент livewire в нем будет доступно свойство $this->referrer
 *
 * @package App\Traits
 */
trait Referrer
{
    #[Locked]
    public string|null $referrer;

    public function mountReferrer(): void
    {
        $this->referrer = currentUrl();
    }
}