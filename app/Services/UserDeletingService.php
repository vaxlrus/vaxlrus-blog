<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        $user->comments()->delete();
        $user->delete();
        DB::commit();
    }

    /**
     * Окончательно удаляет пользователей и их комментарии которые уже нельзя восстановить
     *
     * @return void
     */
    public function completlyDeleteUserAccounts(): void {
        User::withTrashed()->whereDate('deleted_at', '<', now()->subDays(UserRestorationService::PROFILE_RESTORATION_DAYS))->forceDelete();
    }
}
