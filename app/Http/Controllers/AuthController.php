<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth;
use App\Http\Requests\AuthForgotPassword;
use App\Http\Requests\Registration;
use App\Mail\ForgotPassword;
use App\Mail\Registration as RegistrationMail;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    private string $routeActivatingUser = 'auth.activating_user';

    public function registration(Registration $request): JsonResponse
    {
        if (Sentinel::findByCredentials(['email' => $request->get('email')])) {
            return $this->returnError('Користувач вже існує');
        }

        if ($request->get('re_password') != $request->get('password')) {
            return $this->returnError('Помилка. Паролі не співпадають');
        }

        $user = Sentinel::register($request->except(['g_recaptcha_response', 're_password', 'agree']));

        $activation = Activation::create($user);
        $activatingUserUrl = route($this->routeActivatingUser, [$user, $activation->getCode()]);

        Mail::to($user->email)->send(
            new RegistrationMail($user, $request->get('password'), $activatingUserUrl)
        );

        return $this->returnSuccess('Ви успішно зареєстровані. На пошту вислана посилання для активації');
    }

    public function login(Auth $request): JsonResponse
    {
        try {
            $user = Sentinel::authenticate(
                $request->only(['email', 'password']),
                request('remember') ? true : false
            );

            if ($user) {
                return $this->returnSuccess('Ви успішно авторизовані');
            }

            return $this->returnError('Користувача не знайдено або пароль вказано неправильно');
        } catch (\Cartalyst\Sentinel\Checkpoints\NotActivatedException $e) {
            return $this->returnError('Користувач не активований');
        }
    }

    public function forgotPassword(AuthForgotPassword $request): JsonResponse
    {
        $user = Sentinel::findByCredentials(['email' => $request->get('email')]);

        if ($user) {
            $newPassword = Str::random(10);
            Sentinel::update($user, ['password' => $newPassword]);

            Mail::to($user->email)->send(new ForgotPassword($user, $newPassword));

            return $this->returnSuccess('Вам на пошту відправлено новий пароль');
        }

        return $this->returnError('Користувача не знайдено');
    }

    public function activatingUser(string $userId, string $token): View
    {
        try {
            // Попытка найти пользователя по ID
            $user = User::findOrFail($userId); // findOrFail выбросит исключение, если пользователь не найден

            // Проверяем, активирован ли пользователь
            if (Activation::completed($user)) {
                $result = __t('Користувач вже активований');
                $description = str_replace('[link]', route('profile'), __t('Тепер ви можете користуватися <a href="[link]">особистим кабінетом</a>'));
                Sentinel::login($user);

                return view($this->routeActivatingUser, compact('result', 'description'));
            }

            // Если активация успешна
            if (Activation::complete($user, $token)) {
                $result = __t('Ваш профіль успішно активований');
                $description = str_replace('[link]', route('profile'), __t('Тепер ви можете користуватися <a href="[link]">особистим кабінетом</a>'));
                Sentinel::login($user);

                return view($this->routeActivatingUser, compact('result', 'description'));
            }

            // Если код активации неверный
            $result = __t('Помилка. Код активації користувача не підходить');
            $description = __t('Можливо, він застарів. Повторно скористайтеся функцїєю нагадування паролю або зверніться за допомогою до техпідтримки');
        } catch (ModelNotFoundException $e) {
            // Обрабатываем случай, когда пользователь не найден
            $result = __t('Користувача не існує');
            $description = __t('<p>Ця помилка означає, що за вказаним посиланням не знайдено користувача, чий профіль треба активувати.</p> <p>Будь ласка, перевірте правильність посиланная або зверніться до технічної підтримки</p>');
        } catch (\Exception $e) {
            // Обрабатываем любые другие ошибки
            $result = __t('Упс, сталася помилка');
            $description = __t('<p>Вибачте, але під час активації сталася технічна помилка. Ми вже вирішуємо цю проблему.</p> <p>Спробуйте перейти по даному посиланню через декілька секунд. Або зверніться до техпідтримки</p>');
            \Log::warning('Ошибка при активации учетной записи по ссылке активации', [
                'userId' => $userId,
                'token' => $token,
                'error' => $e->getMessage()
            ]);

        }

        return view($this->routeActivatingUser, compact('result', 'description'));
    }

    public function pageLogin(): View
    {
        return view('auth.login');
    }

    public function pageForgotPassword(): View
    {
        return view('auth.forgot_password');
    }

    public function pageRegistration(): View
    {
        return view('auth.registration');
    }
}
