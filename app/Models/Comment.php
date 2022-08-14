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
     * Delete comment
     *
     * @return void
     */
    public function delete(): void
    {
        $this->delete();
    }

    /**
     * Define that comment create date past 1 hour
     *
     * @return bool
     */
    public function isCanBeDeleted(): bool
    {
        $timePeriod = self::COMMENT_DELETION_PERIOD . " m";

        return time() <= strtotime($timePeriod, strtotime($this->created_at));
    }
}
