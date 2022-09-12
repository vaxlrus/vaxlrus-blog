<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;

class UserRestorationService
{
    public const PROFILE_RESTORATION_DAYS = 14;

    /**
     * Восстанавливает аккаунт
     *
     * @param string $email
     * @return void
     */
    public function restoreAccount(string $email): void {
        User::withTrashed()->where('email', $email)->restore();
        $user = User::where('email', $email)->first();

        Comment::withTrashed()->where('user_id', $user->id)->restore();
    }

    /**
     * Проверяет возможность восстановления удаленного аккаунта
     *
     * @param string $email
     * @return bool
     */
    public function isAccountRestorable(string $email): bool {
        $user = User::withTrashed()->where('email', $email)->first();

        // Если по указанной почте вообще не существует пользователя
        if (! $user) {
            return false;
        }

        if ($user->deleted_at >= now()->subDays(self::PROFILE_RESTORATION_DAYS)) {
            return true;
        }

        return false;
    }
}
