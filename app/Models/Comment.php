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
    private int $commentDeletionPeriod = 60;

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
     * @return bool
     */
    public function delete(): bool
    {
        if ( !$this->delete() )
        {
            throw new DatabaseException('Comment not deleted');
        }
    }

    /**
     * Assert that comment create date past 1 hour
     *
     * @return bool
     */
    public function assertIsCanBeDeleted(): bool
    {
        $timePeriod = "+{$this->commentDeletionPeriod} m";

        return time() <= strtotime($timePeriod, strtotime($this->created_at));
    }
}
