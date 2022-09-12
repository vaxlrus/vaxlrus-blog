<?php

namespace App\Services;

use App\Exceptions\InvalidPasswordException;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRestorationService
{
    public const PROFILE_RESTORATION_DAYS = 14;

    /**
     * Восстанавливает аккаунт
     *
     * @param string $email
     * @param string $password
     * @return void
     * @throws InvalidPasswordException
     */
    public function restoreAccount(string $email, string $password): void
    {
        $user = User::withTrashed()->where('email', $email)->first();
        if (!Hash::check($password, $user->password)) {
            throw new InvalidPasswordException('Не верный пароль пользователя');
        }

        $user->restore();
        Comment::withTrashed()->where('user_id', $user->id)->restore();
    }

    /**
     * Проверяет возможность восстановления удаленного аккаунта
     *
     * @param string $email
     * @return bool
     */
    public function isAccountRestorable(string $email): bool
    {
        $user = User::withTrashed()->where('email', $email)->first();

        // Если по указанной почте вообще не существует пользователя
        if (!$user) {
            return false;
        }

        if ($user->deleted_at >= now()->subDays(self::PROFILE_RESTORATION_DAYS)) {
            return true;
        }

        return false;
    }
}
