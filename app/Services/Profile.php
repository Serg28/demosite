<?php

namespace App\Services;

use App\Exceptions\InvalidPasswordException;
use App\Http\Requests\ProfileData;
use App\Http\Requests\ProfilePassword;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Http\JsonResponse;

class Profile
{
    public function save(ProfileData $request): JsonResponse
    {
        $request->merge(['birth' => (strtotime($request->get('birth'))) ? date('Y-m-d H:i:s', strtotime($request->get('birth'))) : null]);

        Sentinel::update(app('user'), $request->only(['first_name', 'last_name', 'patronymic', 'email', 'phone', 'city_id', 'birth']));

        $this->updatePassword($request);

        return response()->json(
            [
                'status' => 'success',
                'message' => __t('Дані успішно оновлені'),
            ]
        );
    }

    public function savePassword(ProfilePassword $request): void
    {
        $oldPassword = $request->get('old_password');
        $newPassword = $request->get('new_password');

        $hasher = Sentinel::getHasher();

        if (! $hasher->check($oldPassword, app('user')->password)) {
            try {
                $request->session()->flash('error_password', __t('Старий пароль невірний'));
            } catch (\Exception $e) {
                throw new InvalidPasswordException();
            }

            return;
        }

        Sentinel::update(app('user'), ['password' => $newPassword]);
        try {
            $request->session()->flash('success_password', __t('Дані успішно оновлені'));
        } catch (\Exception $e) {
            session()->flash('success_password', __t('Дані успішно оновлені'));
        }
    }

    /**
     * @throws InvalidPasswordException
     */
    public function saveFormPassword(ProfilePassword $request): JsonResponse
    {
        $this->savePassword($request);

        return response()->json(
            [
                'status' => 'success',
                'message' => __t('Дані успішно оновлені'),
            ]
        );
    }

    private function updatePassword(ProfileData $request): void
    {
        if ($this->checkUpdatePassword($request)) {
            Sentinel::update(app('user'), ['password' => $request->get('new_password')]);
        }
    }

    private function checkUpdatePassword(ProfileData $request): bool
    {
        return ($request->get('new_password') && $request->get('re_password')) && $request->get('new_password') == $request->get('re_password');
    }
}
