<?php

namespace App\Livewire\Components;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Динамічний менеджер модальних вікон — Livewire 4 native.
 *
 * Сумісний з wire-elements/modal API (події, closeModalWithEvents, maxWidth).
 * Розміщується один раз у shop.blade.php.
 *
 * TODO: Винести в окремий пакет linecore/modal.
 *
 * == Виклик з JS (data-атрибути, livewire-modal.js) ==
 *   <button data-js-modal
 *           data-component="forms.recall"
 *           data-subject="Callback">Дзвінок</button>
 *
 * == Виклик з PHP (з будь-якого Livewire-компонента) ==
 *   $this->dispatch('openModal', component: 'forms.recall', arguments: ['subject' => 'Callback'])
 *   $this->dispatch('closeModal')
 *   $this->dispatch('closeModalWithEvents', events: [['name' => 'cart-updated']])
 */
class ModalManager extends Component
{
    public string $component = '';

    public array $arguments = [];

    public bool $isOpen = false;

    /** CSS-клас для max-width (може перевизначатись у компоненті через modalMaxWidth()) */
    public string $maxWidth = 'max-w-lg';

    #[On('openModal')]
    public function open(string $component, array $arguments = []): void
    {
        $this->component = $component;
        $this->arguments = $arguments;
        $this->maxWidth = $this->resolveMaxWidth($component);
        $this->isOpen = true;
    }

    #[On('closeModal')]
    public function close(): void
    {
        $this->isOpen = false;
        $this->component = '';
        $this->arguments = [];
        $this->maxWidth = 'max-w-lg';
    }

    /**
     * Закриває модалку і диспетчеризує додаткові події.
     * Сумісний з wire-elements/modal closeModalWithEvents.
     *
     * @param  array<array{name: string, params?: array}>  $events
     */
    #[On('closeModalWithEvents')]
    public function closeWithEvents(array $events = []): void
    {
        $this->close();

        foreach ($events as $event) {
            $this->dispatch($event['name'], ...($event['params'] ?? []));
        }
    }

    /**
     * Визначає CSS-клас max-width з компонента (якщо він реалізує modalMaxWidth()).
     * Сумісно з wire-elements/modal ModalComponent::modalMaxWidth().
     */
    protected function resolveMaxWidth(string $component): string
    {
        $class = app('livewire')->getClass($component);

        if ($class && method_exists($class, 'modalMaxWidth')) {
            return $class::modalMaxWidth();
        }

        return 'max-w-lg';
    }

    public function render(): View
    {
        return view('livewire.components.modal-manager');
    }
}
