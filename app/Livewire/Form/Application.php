<?php

namespace App\Livewire\Form;

use Livewire\Component;
use App\Events\FeedbackCreate;
use App\Models\Feedback as ModelFeedback;
use LivewireUI\Modal\ModalComponent;

use Livewire\Attributes\Validate;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

class Application extends ModalComponent {

    #[Validate('required', message: 'Обязательное поле!')]
    public string|null $name;

    #[Rule('required|between:4,22')]
    public string|null $phone;

    #[Rule('required|email')]
    public string|null $email;

    #[Rule('required|between:2,2048')]
    public string|null $comment;

    public string|null $g_recaptcha_response;
    public mixed $recaptcha;
    public string|null $formId;
    public string|null $referrer;

    public string|null $subject;

    public int|null $user_id;

    public function mount($recaptcha = true, string $formId = '', string $subject = 'empty') {
        $this->user_id = app('user')->id ?? null;

        $this->recaptcha = !(($recaptcha === 'false' || $recaptcha === false));
        $this->formId = $formId ?: strtolower(class_basename($this)).'_component';
        $this->referrer = geturl(\Request::fullUrl());
        $this->subject = $subject;
    }

    public function rendered(){
        $this->dispatch('application-initialized');
    }


    #[On('recaptcha-changed')]
    public function setCaptcha($value) {
        $this->g_recaptcha_response = $value;
    }
    public static function modalMaxWidth(): string
    {
        return 'recall-popup';
    }

    public static function modalMaxWidthClass(): string {
        return 'recall-popup';
    }

    private function getReferrer(): array|string|null {
        return (\Request::path() === 'livewire/update') ? request()->header('referer') : geturl(\Request::fullUrl());
    }

    public function submit() {
        $this->validate([
            'name' => 'required|between:2,64',
            'phone' => 'required|between:4,22',
            //'email' => 'required|email',
            'comment' => 'required|between:2,2048',
            //'checkbox' => 'required',
            'g_recaptcha_response' => $this->recaptcha ? 'required|recaptcha' : '',
        ]);

        try {
            $feedback = ModelFeedback::create($this->except(['g_recaptcha_response','recaptcha']));
            FeedbackCreate::dispatch($feedback);

            $this->resetForm();

            $this->dispatch('openModal', component: 'ModalBlock', arguments: ['title' => __t("Успех"),'text' => __t("Ваша заявка успешно отправлена!") ,'class' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('openModal', component: 'ModalBlock', arguments: ['title' => __t("Ошибка"),'text' => __t("Обратитесь к администратору, если ошибка повторится!") ,'class' => 'error']);
        }
    }

    private function resetForm(): void {
        $subject = $this->subject;
        $this->reset();
        $this->subject = $subject;
    }

    public function render()
    {
        return view('livewire.form.application');
    }
}
