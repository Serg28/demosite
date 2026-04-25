<?php

namespace App\Livewire\Form;

use App\Events\FeedbackCreate;
use App\Models\Feedback as ModelFeedback;
use App\Traits\LivewireRecaptchable;
use App\Traits\Referrer;
use LivewireUI\Modal\ModalComponent;

use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;

class Feedback extends ModalComponent
{
    use LivewireRecaptchable;
    use Referrer;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:64', message: 'Занадто довгий')]
    public string|null $name;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:64', message: 'Занадто довгий')]
    public string|null $second_name;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('email', message: 'Це не Email')]
    public string|null $email;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:2048', message: 'Занадто довгий')]
    public string|null $comment;

    public string|null $subject;

    #[Locked]
    public int|null $user_id;

    public function mount(string $subject = 'empty')
    {
        $this->user_id = app('user')->id ?? null;
        $this->subject = $subject;
    }

    public function submit(): void
    {
        $this->validate($this->getRules());

        try {
            $feedback = ModelFeedback::create($this->except(['g_recaptcha_response', 'recaptcha']));//'checkbox',
            FeedbackCreate::dispatch($feedback);

            $this->resetForm();

            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Дякуємо"),
                'text' => __t("Ваше повідомлення успішно надіслано"),
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при отправке сообщения на странице Контакты: ' . $e->getMessage());
            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Помилка"),
                'text' => __t("Не вдалося надіслати ваше повідомлення з технічних причин. Спробуйте знову трохи пізніше"),
                'class' => 'error'
            ]);
        }
    }

    private function resetForm(): void
    {
        $subject = $this->subject;
        $this->reset();
        $this->subject = $subject;
    }

    public function render()
    {
        return view('livewire.form.feedback');
    }
}
