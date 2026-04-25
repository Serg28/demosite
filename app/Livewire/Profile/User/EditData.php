<?php
namespace App\Livewire\Profile\User;

use App\Http\Requests\ProfileData;
use App\Models\User;
use App\Services\Profile;
use App\Services\UserService;
use App\Traits\LivewireRecaptchable;
use Illuminate\Http\Request;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;

class EditData extends ModalComponent
{
    use LivewireRecaptchable;

    private UserService $userService;

    private $user;

    // Публичные свойства для привязки к форме
    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $last_name = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $first_name = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Не меньше 2 символів')]
    #[Validate('max:40', message: 'Не більше 40 символів')]
    public string|null $patronymic = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:18', message: 'Поле має бути повністю заповнене')]
    #[Validate('max:18', message: 'Поле має бути повністю заповнене')]
    public string|null $phone = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('email', message: "Невірний формат email")]
    public string|null $email = '';

    public static function modalMaxWidth(): string
    {
        return 'edit-acc-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'edit-acc-popup';
    }

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
        $this->user = app('user');
    }

    public function submit(Profile $profile)
    {
        $this->validate($this->getRules());

        // Проверка авторизации и существование пользователя
        $authUserId = $this->user?->id;
        $checkUser = User::find($authUserId);

        if (!$authUserId || !$checkUser) {
            abort(403, 'У вас нет прав для редактирования этого профиля.');
        }

        try {
            // Создаем базовый объект Request из данных компонента
            $baseRequest = Request::create('/', 'POST', $this->only('email', 'first_name', 'last_name', 'patronymic', 'phone'));

            // Создаем объект ProfileData из базового Request
            $profileData = ProfileData::createFromBase($baseRequest);

            // Вызов метода сервиса Profile с использованием ProfileData
            $profile->save($profileData);

            // Закрываем модальное окно и показываем сообщение об успехе
            session()->flash('success', 'Ваші дані успішно оновлено.');

            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Дякуємо"),
                'text' => __t("Ваші дані успішно оновлено."),
                'class' => 'success'
            ]);

            $this->dispatch('user-profile-updated');

        } catch (\Exception $e) {
            session()->flash('error', 'Сталася помилка під час оновлення даних. Спробуйте через деякий час');
            $this->notify(__t("Сталася помилка під час оновлення даних. Спробуйте через деякий час"), __t('Помилка'), 'error');
        }
    }

    public function render()
    {
        // Проверка на авторизацию
        if (!$this->user) {
            abort(403, 'Доступ запрещен');
        }

        // Получение данных текущего пользователя
        $user = $this->user->toArray();

        // Заполнение свойств данными пользователя
        $this->email = $user['email'] ?? '';
        $this->phone = $user['phone'] ?? '';
        $this->first_name = $user['first_name'] ?? '';
        $this->last_name = $user['last_name'] ?? '';
        $this->patronymic = $user['patronymic'] ?? '';

        return view('livewire.profile.user.edit-data');
    }
}
