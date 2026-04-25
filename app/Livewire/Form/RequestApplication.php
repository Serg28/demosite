<?php

namespace App\Livewire\Form;

use App\Models\Feedback;
use App\Events\FeedbackCreate;
use App\Traits\LivewireRecaptchable;
use App\Traits\Referrer;
use Livewire\Component;

use Livewire\Attributes\Validate;


class RequestApplication extends Component
{

    use LivewireRecaptchable;
    use Referrer;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:68', message: 'Занадто довгий')]
    public string|null $name;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:68', message: 'Занадто довгий')]
    public string|null $second_name;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:4', message: 'Занадто короткий')]
    #[Validate('max:22', message: 'Занадто довгий')]
    public string|null $phone;

    #[Validate('required', message: "Обов'язкове поле")]
    //#[Validate('min:2', message: 'Занадто короткий')]
        //#[Validate('max:2048', message: 'Занадто довгий')]
    public string|null $theme;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:2048', message: 'Занадто довгий')]
    public string|null $comment;

    public string|null $subject;

    public int|null $user_id;

    public function boot()
    {
        $this->user_id = app('user')->id ?? null;
    }

    public function rendered()
    {
        $this->dispatch('request-application-initialized');
    }

    public static function modalMaxWidth(): string
    {
        return 'recall-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'recall-popup';
    }

    public function submit()
    {
        $this->validate($this->getRules());

        try {
            $feedback = Feedback::create($this->except(['g_recaptcha_response', 'recaptcha']));

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

    private function resetForm(): void
    {
        $subject = $this->subject;
        $this->reset();
        $this->subject = $subject;
    }

    public function render()
    {
        return view('livewire.form.request-application');
    }
}
