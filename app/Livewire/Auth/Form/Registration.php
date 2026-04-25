<?php

namespace App\Livewire\Auth\Form;

use Livewire\Attributes\Validate;
use Livewire\Component;
use LivewireUI\Modal\ModalComponent;
use App\Services\UserService;
use App\Traits\LivewireRecaptchable;
use Livewire\Attributes\Rule;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Registration extends ModalComponent
{
    use LivewireRecaptchable;

    private UserService $userService;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $last_name = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $first_name = '';

    public string|null $patronymic = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:18', message: 'Поле має бути повністю заповнене')]
    #[Validate('max:18', message: 'Поле має бути повністю заповнене')]
    public string|null $phone = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('email', message: "Невірний формат email")]
    public string|null $email = '';

    #[Validate('required', message: "Обов'язкове поле")]
    public string|null $password;

    #[Validate('required', message: "Обов'язкове поле")]
    public string|null $re_password;

    #[Validate('required', message: "Потрібна згода з умовами")]
    public array $checkbox = [];

    public function boot(userService $userService): void
    {
        $this->userService = $userService;
    }

    public static function modalMaxWidth(): string
    {
        return 'forgot';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'forgot';
    }

    public function submit(): void
    {
        try {

            // Валидация полей
            $this->validate($this->getRules());

            // 1. Проверка email на зарегистрированность
            if (Sentinel::findByCredentials(['email' => trim($this->email)])) {
                $this->notify(__t("Цей email вже зареєстрований."), __t('Помилка'), 'info');
                return;
            }

            // 2. Проверка телефона на зарегистрированность
            if (Sentinel::findByCredentials(['phone' => trim($this->phone)])) {
                $this->notify(__t("Цей номер вже зареєстрований."), __t('Помилка'), 'info');
                return;
            }

            // 3. Проверка совпадения паролей
            if ($this->password !== $this->re_password) {
                $this->notify(__t("Паролі не співпадають!"), __t('Помилка'), 'info');
                return;
            }

            // 4. Регистрация пользователя через сервис
            $user = $this->userService->registration($this->only(['email', 'first_name', 'last_name', 'patronymic', 'phone', 'password']));

            if ($user) {
                $this->dispatch('closeModal');
                //$this->notify(__t("Ви успішно зареєструвалися! Через декілька хвилин на Вашу электронну адресу надійде лист з інструкцїєю для активації облікового запису"), __t('Успех'), 'success');

                $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                    'title' => __t("Дякуємо"),
                    'text' => str_replace('[email]', $this->email, __t("Ви успішно зареєструвалися! Для активації профілю прохання перейти за посиланням з листа, який надійде на вказану Вами электронну адресу <strong>[email]</strong>")) ,
                    'class' => 'success'
                ]);
                //$this->redirect(currentUrl());
            } else {
                // Если регистрация не удалась (например, ошибка создания пользователя)
                $this->notify(__t("Нажаль, сталася помилка під час реєстрації. Спробуйте ще раз."), __t('Помилка'), 'error');
                \Log::info('Ошибка при регистрации пользователя', [
                    'email' => $this->email,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'phone' => $this->phone,
                    'password' => $this->password,
                ]);
            }
        } catch (\Exception $e) {
            $this->notify(__t("Нажаль, сталася помилка під час реєстрації. Спробуйте ще раз.!"), __t('Помилка'), 'error');
        }
    }

    public function render()
    {
        return view('livewire.auth.form.registration');
    }
}
