<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /**
     * @var int Comment deletion period (in minutes)
     */
    public const COMMENT_DELETION_PERIOD = 60;

    use HasFactory, SoftDeletes;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define that comment create date past 1 hour
     *
     * @param User User who tries to delete comment
     * @return bool
     */
    public function isCanBeDeletedByUser(?User $user): bool
    {
        // Если пользователь не авторизован в системе
        if (!$user) {
            return false;
        }

        // Если текущий пользователь админ, то может удалять любые комментарии
        if ($user->isAdmin()) {
            return true;
        }

        $commentDeletableUntill = strtotime($this->created_at) + self::COMMENT_DELETION_PERIOD * 60;
        $commentAuthor = $this->author;

        // Если это обычный пользователь и это не его комментарий
        if ($user->id !== $commentAuthor->id) {
            return false;
        }

        // Если это обычный пользователь, это его комментарий, то определить доступно ли удаление по времени
        return time() <= $commentDeletableUntill;
    }
}
