<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class UserDeletingService
{
    public const PROFILE_RESTORATION_DAYS = 14;

    /**
     * Удалить пользователя и его комментарии
     *
     * @param User|Authenticatable $user
     * @return void
     */
    public function delete(User|Authenticatable $user): void {
        $user->delete();
        $user->comments()->delete();
    }

    /**
     * Проверяет возможность восстановления удаленного аккаунта
     *
     * @param string $email
     * @return bool
     */
    public function isAccountRestorable(string $email): bool {
        $user = User::withTrashed()->where('email', $email)->first();

        if ($user->deleted_at >= now()->subDays(self::PROFILE_RESTORATION_DAYS)) {
            return true;
        }

        return false;
    }

    /**
     * Окончательно удаляет пользователей и их комментарии которые уже нельзя восстановить
     *
     * @return void
     */
    public function completlyDeleteUserAccounts(): void {
        User::withTrashed()->whereDate('deleted_at', '<', now()->subDays(UserDeletingService::PROFILE_RESTORATION_DAYS))->forceDelete();
    }
}
