<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Mail\ForgotPassword;
use App\Mail\Registration as RegistrationMail;
use App\Models\User;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

//use App\Http\Requests\{Auth, AuthForgotPassword, Registration};

class UserService extends BaseController
{
    public string $routeActivatingUser = 'auth.activating_user';

    /*
    public function registration($request)
    {
        if (!$request->email) {
            return false;
        }

        if ($user = Sentinel::findByCredentials(['email' => trim($request->email)])) {
            return $user;
        }

        $fields = [
            'email' => trim($request->email),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'password' => Str::random(8),
        ];

        $user = Sentinel::register($fields);

        $activation = Activation::create($user);
        $activatingUserUrl = route($this->routeActivatingUser, [$user, $activation->getCode()]);

        Mail::to($user->email)->send(
            new RegistrationMail($user, $fields['password'], $activatingUserUrl)
        );

        return $user;
    }*/

    public function registration(array $request)
    {
        if (!$request['email']) {
            return false;
        }

        if ($user = Sentinel::findByCredentials(['email' => trim($request['email'])])) {
            return $user;
        }

        $fields = [
            'email' => trim($request['email']),
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone' => $request['phone'],
            'password' => $request['password'] ?? Str::random(8),
        ];

        $user = Sentinel::register($fields);

        $activation = Activation::create($user);
        $activatingUserUrl = route($this->routeActivatingUser, [$user, $activation->getCode()]);

        if (isset($user->email)) {
            Mail::to($user->email)->send(
                new RegistrationMail($user, $fields['password'], $activatingUserUrl)
            );
        }

        return $user;
    }

    /**
     * Аутентификация пользователя по предоставленным учетным данным.
     *
     * Этот метод пытается аутентифицировать пользователя, используя предоставленные учетные данные
     * (как правило, адрес электронной почты и пароль). Для выполнения аутентификации используется
     * метод `authenticate` из Cartalyst Sentinel.
     *
     * @param array $credentials Ассоциативный массив с данными учетной записи пользователя.
     *                           Должен содержать ключи 'email' и 'password'.
     * @param bool $remember Флаг, указывающий, нужно ли запомнить сеанс пользователя (необязательно, по умолчанию false).
     *
     * @return bool Возвращает true, если аутентификация прошла успешно, в противном случае - false.
     *
     * @throws \Cartalyst\Sentinel\Checkpoints\NotActivatedException Если учетная запись пользователя не активирована.
     */
    public function loginUser(array $credentials, bool $remember = false): bool
    {
        try {
            // Попытка аутентификации пользователя с использованием Cartalyst Sentinel
            $user = Sentinel::authenticate($credentials, $remember);
            // Возврат true, если аутентификация успешна, в противном случае - false
            return $user !== false;

        } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
            // Перехват исключения NotActivatedException и возврат false
            return false;
        }
    }

    public function loginByUserObject(User $user, $remember = false)
    {
        try {
            if(!Activation::exists($user)) {
                Sentinel::activate($user);
                $role = Sentinel::findRoleByName(User::defaultGroup()->name);
                $role->users()->attach($user);
            }
            return Sentinel::login($user, $remember);
        } catch (NotActivatedException $e) {
            // Перехват исключения NotActivatedException и возврат false
            return false;
        }
    }

    /**
     * Сбрасывает пароль пользователя и отправляет новый пароль на его email.
     *
     * @param array $credentials Массив с данными, содержащими email пользователя.
     *
     * @return JsonResponse JSON-ответ, информирующий о результате операции.
     *
     * @throws \Exception В случае возникновения ошибки при работе с данными или отправке email.
     *
     * Описание процесса:
     * - Находит пользователя по email.
     * - Генерирует код для сброса пароля (Reminder).
     * - Генерирует новый случайный пароль.
     * - Сбрасывает пароль и отправляет его пользователю на почту.
     * - Возвращает сообщение об успехе или ошибке.
     */
    public function forgotPassword(array $credentials): JsonResponse
    {
        try {
            // Находим пользователя по email
            $user = Sentinel::findByCredentials(['email' => $credentials['email']]);

            if ($user) {
                // Создаем код для сброса пароля
                $reminder = Reminder::create($user);

                // Новый пароль
                $newPassword = Str::random(10);

                // Завершаем сброс пароля, используя код и новый пароль
                if (Reminder::complete($user, $reminder->code, $newPassword)) {
                    // Отправляем новый пароль на почту пользователя
                    Mail::to($credentials['email'])->send(new ForgotPassword($user, $newPassword));

                    return ResponseService::success('Вам на пошту відправлено новий пароль');
                }

                return ResponseService::error('Не вдалося скинути пароль. Спробуйте пізніше.');
            }

            return ResponseService::error('Користувач не знайдений');
        } catch (\Exception $e) {
            // Обработка любых исключений
            return ResponseService::error('Виникла помилка: ' . $e->getMessage());
        }
    }

    /**
     * Получить идентификатор пользователя или создать нового, если не найден.
     *
     * @param array $request Данные запроса, которые могут использоваться при регистрации.
     * @param bool $registerIfNotFound Если установлен в true, то производится регистрация пользователя,
     *                                 если идентификатор пользователя не найден.
     * @return int Возвращает идентификатор пользователя или 0, в зависимости от наличия пользователя и параметра $registerIfNotFound.
     */
    public function getUserOrCreate(array $request, bool $registerIfNotFound = true): int
    {
        $userData = $this->getUserData();

        if (isset($userData['id'])) {
            return $userData['id'];
        }

        if ($registerIfNotFound && $request) {
            return $this->registration($request)->id;
        }

        return 0;
    }


    public function getUserData()
    {
        return app('user') ? collect(app('user')) : collect();
    }
}
