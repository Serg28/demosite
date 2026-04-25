<?php

namespace App\Livewire\Auth\Form;

use Livewire\Component;
use LivewireUI\Modal\ModalComponent;

class Delete extends ModalComponent {
    #[Rule('required|email')]
    public string|null $email;
    public string|null $g_recaptcha_response;
    public mixed $recaptcha;
    public string|null $formId;
    public int|null $user_id;

    public function mount($recaptcha = true, string $formId = '') {
        $this->user_id = app('user')->id ?? null;
        $this->recaptcha = !(($recaptcha === 'false' || $recaptcha === false));
        $this->formId = $formId ?: strtolower(class_basename($this)).'_component';
    }

    #[On('recaptcha-changed')]
    public function setCaptcha($value): void { $this->g_recaptcha_response = $value; }
    public static function modalMaxWidth(): string  { return 'delete'; }
    public static function modalMaxWidthClass(): string  { return 'delete'; }

    public function submit(): void {
        $this->validate([
            'email' => 'required|email',
            'g_recaptcha_response' => $this->recaptcha ? 'required|recaptcha' : ''
        ]);

        try {
            //$this->reset();

            $this->dispatch('openModal', component: 'ModalBlock', arguments: ['title' => __t("Успех"),'text' => __t("Ваша заявка успешно отправлена!") ,'class' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('openModal', component: 'ModalBlock', arguments: ['title' => __t("Ошибка"),'text' => __t("Обратитесь к администратору, если ошибка повторится!") ,'class' => 'error']);
        }

    }

    public function render() {
        return view('livewire.auth.form.delete');
    }

    public function rendered(){
        $this->dispatch('auth-delete-initialized');
    }
}
