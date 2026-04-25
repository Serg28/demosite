<?php

namespace App\Livewire\Auth\Form;

use App\Services\UserService;
use App\Traits\LivewireRecaptchable;
use Livewire\Attributes\Rule;
use LivewireUI\Modal\ModalComponent;

class Login extends ModalComponent
{
    use LivewireRecaptchable;

    private userService $userService;

    #[Rule('required|email')]
    public string|null $email;

    #[Rule('required|between:2,64')]
    public string|null $password;

    public function boot(userService $userService): void
    {
        $this->userService = $userService;
    }

    public static function modalMaxWidth(): string
    {
        return 'login-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'login-popup';
    }

    public function registerForm(): void
    {
        $this->dispatch('openModal', component: 'auth.form.registration');
    }

    public function forgotForm(): void
    {
        $this->dispatch('openModal', component: 'auth.form.forgot');
    }

    public function submit(): void
    {
        $this->validate($this->getRules());

        try {
            $result = $this->userService->loginUser($this->only(['email', 'password']));

            if ($result) {
                $this->dispatch('closeModal');
                $this->notify(__t("Ви успішно авторизовані"), __t('Дякуємо'), 'success');
                $this->redirect(currentUrl());
            } else {
                //$this->notify(__t("Користувача з таким email або паролем не знайдено. Будь ласка, перевірте введені дані."), __t('Ошибка'), 'error');
                // Авторизация неуспешна
                /*$this->dispatch('openModal', component: 'ModalBlock', arguments: [
                    'title' => __t("Ошибка"),
                    'text' => __t("Користувача з таким email або паролем не знайдено. Будь ласка, перевірте введені дані."),
                    'class' => 'error'
                ]);*/
                session()->flash('errorLogin',
                    __t("Користувача з таким email або паролем не знайдено. Будь ласка, перевірте введені дані."));
            }
        } catch (\Exception $e) {
            $this->notify(__t("Спробуйте знову через декілька хвилин"), __t('Технічна помилка'), 'error');
            // Ошибка при авторизации
            /*$this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Ошибка"),
                'text' => __t("Вибачте, відбулася технічна помилка. Спробуйте ще раз."),
                'class' => 'error'
            ]);*/
            //session()->flash('errorLogin', __t("Вибачте, відбулася технічна помилка. Спробуйте ще раз."));
        }
    }

    public function render()
    {
        return view('livewire.auth.form.login');
    }

    public function rendered()
    {
        $this->dispatch('auth-login-initialized');
    }
}
