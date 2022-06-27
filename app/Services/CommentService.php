<?php

namespace App\Services;

use App\Exceptions\Comment\UnableToDeleteException;
use App\Exceptions\Database\DatabaseException;
use App\Exceptions\NotFoundException;
use App\Models\Comment;

class CommentService
{
    /**
     * Delete comment
     */
    public function delete(int $id, bool $adminDelete = false): void
    {
        // Get comment model
        $comment = Comment::find($id);

        if ( !$comment )
        {
            throw new NotFoundException('Didn\'t find comment with id = {$id}');
        }

        if ( !$adminDelete && !$comment->assertIsCanBeDeleted() )
        {
            throw new UnableToDeleteException('You can\'t delete comment after 1 hour from their posting');
        }

        $comment->delete();
    }
}
