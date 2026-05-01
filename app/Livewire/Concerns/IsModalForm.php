<?php

namespace App\Livewire\Concerns;

/**
 * Трейт для Livewire-компонентів, що відображаються в ModalManager.
 *
 * API сумісний з wire-elements/modal ModalComponent:
 *   - closeModal()
 *   - closeModalWithEvents()
 *   - modalMaxWidth() — перевизначається у компоненті
 *   - destroyOnClose() — завжди true (компонент видаляється при закритті)
 *
 * TODO: При виносі ModalManager у пакет linecore/modal — трейт стане частиною пакету.
 *
 * @example
 *   class RecallForm extends Component {
 *       use IsModalForm;
 *       public static function modalMaxWidth(): string { return 'max-w-md'; }
 *   }
 */
trait IsModalForm
{
    public function closeModal(): void
    {
        $this->dispatch('closeModal');
    }

    /**
     * Закрити модалку і диспетчеризувати додаткові події.
     *
     * @param  array<array{name: string, params?: array}>  $events
     */
    public function closeModalWithEvents(array $events): void
    {
        $this->dispatch('closeModalWithEvents', events: $events);
    }

    /**
     * CSS Tailwind клас для max-width модалки.
     * Перевизначайте у конкретному компоненті.
     */
    public static function modalMaxWidth(): string
    {
        return 'max-w-lg';
    }
}
