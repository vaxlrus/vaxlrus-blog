<?php

namespace App\Services;

use App\Exceptions\Comment\NotFoundException;
use App\Exceptions\Comment\UnableToDeleteException;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class CommentService
{
    /**
     * Delete comment
     */
    public function delete(int $id, User|Authenticatable $user): void
    {
        // Get comment model
        $comment = Comment::find($id);

        if (!$comment) {
            throw new NotFoundException("Didn\'t find comment with id = {$id}");
        }

        if (!$comment->isCanBeDeletedByUser($user)) {
            throw new UnableToDeleteException('You can\'t delete comment after ' . Comment::COMMENT_DELETION_PERIOD . 'minutes from their posting');
        }

        $comment->delete();
    }
}
