<?php

namespace App\Livewire\Form;

use App\Events\FeedbackCreate;
use App\Models\Feedback as ModelFeedback;
use App\Traits\LivewireRecaptchable;
use App\Traits\Referrer;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use LivewireUI\Modal\ModalComponent;

class Recall extends ModalComponent
{

    use LivewireRecaptchable;
    use Referrer;

    #[Rule('required|between:2,64')]
    public string|null $name;

    #[Rule('required|between:4,22')]
    public string|null $phone;

    public string|null $subject;

    #[Locked]
    public int|null $user_id;

    public function mount(string $subject = ''): void
    {
        $this->user_id = app('user')->id ?? null;
        $this->subject = $subject;
    }

    public static function modalMaxWidth(): string
    {
        return 'recall-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'recall-popup';
    }

    public function submit(): void
    {
        $this->validate($this->getRules());

        try {
            $feedback = ModelFeedback::create($this->except(['g_recaptcha_response', 'recaptcha']));
            FeedbackCreate::dispatch($feedback);

            $this->resetForm();

            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Успех"),
                'text' => __t("Ваша заявка успешно отправлена!"),
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Ошибка"),
                'text' => __t("Обратитесь к администратору, если ошибка повторится!"),
                'class' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.form.recall');
    }

    private function resetForm(): void
    {
        $subject = $this->subject;
        $this->reset();
        $this->subject = $subject;
    }
}
