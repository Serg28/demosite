<?php
namespace App\Livewire\Profile\User;

use App\Http\Requests\ProfilePassword;
use App\Models\User;
use App\Services\Profile;
use App\Services\UserService;
use App\Traits\LivewireRecaptchable;
use Illuminate\Http\Request;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;

class EditPassword extends ModalComponent
{
    use LivewireRecaptchable;

    private UserService $userService;

    private $user;

    // Публичные свойства для привязки к форме
    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $old_password = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $new_password = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $re_password = '';

    private Profile $profileService;

    public static function modalMaxWidth(): string
    {
        return 'edit-acc-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'edit-acc-popup';
    }

    public function boot(UserService $userService, Profile $profileService)
    {
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->user = app('user');
    }

    public function submit()
    {
        $this->validate($this->getRules());

        // Проверка, что пользователь авторизован
        $authUserId = $this->user?->id;

        if (!$authUserId || !User::find($authUserId)) {
            abort(403, 'У вас нет прав для редактирования этого профиля.');
        }

        // Проверка на совпадение нового пароля и его подтверждения
        if ($this->new_password !== $this->re_password) {
            session()->flash('error_password', 'Новий пароль та його підтвердження не співпадають.');
            return;
        }

        try {

            // Создаем базовый объект Request из данных компонента
            $baseRequest = Request::create('/', 'POST', $this->only('old_password', 'new_password', 're_password'));

            // Создаем объект ProfileData из базового Request
            $request = ProfilePassword::createFromBase($baseRequest);

            // Вызов метода смены пароля через сервис профиля
            $this->profileService->savePassword($request);

            // Успешное завершение
            session()->flash('success', 'Пароль успішно змінено.');

            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Дякуємо"),
                'text' => __t("Пароль успішно змінено"),
                'class' => 'success'
            ]);

        } catch (\Exception $e) {
            session()->flash('error_password', 'Сталася помилка під час зміни пароля. '.$e->getMessage());
        }
    }

    public function render()
    {
        // Проверка на авторизацию
        if (!$this->user) {
            abort(403, 'Доступ заборонено');
        }

        return view('livewire.profile.user.edit-password');
    }
}
