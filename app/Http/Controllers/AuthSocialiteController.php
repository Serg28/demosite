<?php

namespace App\Http\Controllers;

use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as UserSocialite;
use Laravel\Socialite\Facades\Socialite;

class AuthSocialiteController extends Controller
{
    public function facebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback(): RedirectResponse
    {
        $userSocialite = Socialite::driver('facebook')->user();

        return $this->login($userSocialite);
    }

    /*public function google(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(): RedirectResponse
    {
        $userSocialite = Socialite::driver('google')->user();

        return $this->login($userSocialite);
    }*/

    public function google(): RedirectResponse
    {
        try {
            request()->session()->put('previous_url', request()->headers->get('referer'));
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            \Log::error('Google auth error google: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Произошла ошибка при входе через Google');
        }
    }

    public function googleCallback(): RedirectResponse
    {
        try {
            $userSocialite = Socialite::driver('google')->user();
            return $this->login($userSocialite);
        } catch (\Exception $e) {
            \Log::error('Google auth error googleCallback: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Произошла ошибка при обработке данных Google');
        }
    }

    private function login(UserSocialite $userSocialite): RedirectResponse
    {
        // Находим пользователя по email
        $user = User::where('email', $userSocialite->user['email'])->first();

        // Если пользователь не найден, регистрируем его
        if (!$user) {
            // Скачиваем аватар пользователя
            $image = file_get_contents($userSocialite->getAvatar());
            $fileImage = 'storage/avatars/' . $userSocialite->user['id'] . '.jpg';
            file_put_contents(public_path($fileImage), $image);

            // Регистрируем и активируем нового пользователя
            $user = Sentinel::registerAndActivate([
                'email' => $userSocialite->user['email'],
                'password' => Str::random(10),
                'first_name' => $this->getFirstName($userSocialite),
                'last_name' => $this->getLastName($userSocialite),
                'image' => '/' . $fileImage,
            ]);
        }

        // Проверяем, активирован ли пользователь
        if (!Activation::completed($user)) {
            // Проверяем наличие активации
            $activation = Activation::exists($user);

            if ($activation) {
                Activation::remove($user);
                $activation = Activation::create($user);
                // Если активация существует, завершаем её с использованием кода
                Activation::complete($user, $activation->code);
            } else {
                // Если активации нет, создаем новую
                $activation = Activation::create($user);
                // Завершаем её с использованием нового кода
                Activation::complete($user, $activation->code);
            }
        }

        // Назначаем роль пользователю, если роль еще не назначена
        //if ($role = Sentinel::findRoleByName(User::defaultGroup()?->name)) {
        //    if (!$role->users()->where('user_id', $user->id)->exists()) {
        //        $role->users()->attach($user);
        //    }
        //}

        // Авторизуем пользователя
        Sentinel::login($user);

        // Возвращаем пользователя на предыдущую страницу
        $previousUrl = request()->session()->get('previous_url'); // Получаем предыдущий URL из сессии
        request()->session()->forget('previous_url'); // Очищаем сохраненный URL из сессии
        return redirect()->to($previousUrl); // Перенаправляем пользователя на предыдущий URL
    }

    public function getFirstName(UserSocialite $user): string
    {
        return $user->user['given_name'] ?? $this->getNameFromSocialite($user->user['name'], true);
    }

    public function getLastName(UserSocialite $user): string
    {
        return $user->user['family_name'] ?? $this->getNameFromSocialite($user->user['name']);
    }

    public function getNameFromSocialite(string $name, bool $isFirst = false): string
    {
        return trim(strstr($name, ' ', $isFirst));
    }
}