<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;

class UserRestorationService
{
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
}
