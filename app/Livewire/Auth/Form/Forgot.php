<?php

namespace App\Livewire\Auth\Form;

use App\Services\UserService;
use App\Traits\LivewireRecaptchable;
use Livewire\Attributes\Rule;
use LivewireUI\Modal\ModalComponent;

class Forgot extends ModalComponent
{
    use LivewireRecaptchable;

    #[Rule('required|email')]
    public string|null $email;

    private userService $userService;

    public function boot(userService $userService): void
    {
        $this->userService = $userService;
    }

    public function mount($recaptcha = true, string $formId = ''): void
    {
        $this->initRecaptcha();
    }

    public static function modalMaxWidth(): string
    {
        return 'forgot';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'forgot';
    }

    public function openLogin(): void
    {
        $this->dispatch('openModal', component: 'auth.form.login', arguments: ['recaptcha' => false]);
    }


    public function submit(): void
    {
        $this->validate($this->getRules());

        try {
            $result = $this->userService->forgotPassword([
                'email' => $this->email
            ])->getData(true);

            if($result['status'] === 'success') {
                //$this->notify($result['message'], __t('Успех'), 'success');
                $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                    'title' => __t("Дякуємо"),
                    'text' => $result['message'],
                    'class' => 'success'
                ]);
            } else {
                session()->flash('errorForget', $result['message']);
            }
        } catch (\Exception $e) {
            /*$this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Ошибка"),
                'text' => __t("Обратитесь к администратору, если ошибка повторится!"),
                'class' => 'error'
            ]);*/
           // session()->flash('errorForget', __t("Вибачте, відбулася технічна помилка. Спробуйте ще раз."));
            $this->notify(__t("Вибачте, відбулася технічна помилка. Спробуйте ще раз."), __t('Технічна помилка'), 'error');
        }
    }

    public function render()
    {
        return view('livewire.auth.form.forgot');
    }

    public function rendered(){
        $this->dispatch('auth-forgot-initialized');
    }
}
