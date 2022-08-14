<?php

namespace App\Models;

use App\Exceptions\Database\DatabaseException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * @var int Comment deletion period (in minutes)
     */
    CONST COMMENT_DELETION_PERIOD = 60;

    use HasFactory;

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
     * @return bool
     */
    public function isCanBeDeleted(): bool
    {
        $currentAppUser = User::find(auth()->id());

        // Если пользователь не авторизован в системе
        if (!$currentAppUser) {
            return false;
        }

        $commentDeletableUntill = strtotime($this->created_at) + self::COMMENT_DELETION_PERIOD * 60;
        $commentAuthor = Comment::find($this->id)->author;

        // Если текущий пользователь админ, то может удалять любые комментарии
        if ($currentAppUser->isAdmin()) {
            return true;
        }

        // Если это обычный пользователь и это не его комментарий
        if ($currentAppUser->id != $commentAuthor->id) {
            return false;
        }

        // Если это обычный пользователь, это его комментарий, то определить доступно ли удаление по времени
        return time() <= $commentDeletableUntill;
    }

    public function isCanNotBeDeleted(): bool
    {
        return !$this->isCanBeDeleted();
    }
}
