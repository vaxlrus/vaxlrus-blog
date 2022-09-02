<?php

namespace App\Http\Controllers;

use App\Exceptions\Comment\NotFoundException;
use App\Exceptions\Comment\UnableToDeleteException;
use App\Models\Post;
use App\Services\CommentService;

class PostCommentsController extends Controller
{
    public function store(Post $post)
    {
        request()->validate([
            'body' => 'required'
        ]);

        $post->comments()->create([
            'user_id' => request()->user()->id,
            'body' => request('body')
        ]);

        return back();
    }

    /**
     * Удаление комментария
     *
     * @param int $id Идентификатор комментария
     * @param CommentService $commentService
     */
    public function destroy(CommentService $commentService, $slug, $id)
    {
        try {
            $user = auth()->user();
            $commentService->delete(intval($id), $user);
        }
        catch (UnableToDeleteException|NotFoundException $e) {
            return back()->withErrors([
                'deletion_unavailable' => $e->getMessage()
            ]);
        }

        return redirect()->back();
    }
}
