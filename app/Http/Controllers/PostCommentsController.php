<?php

namespace App\Http\Controllers;

use App\Exceptions\Comment\UnableToDeleteException;
use App\Exceptions\NotFoundException;
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
     * @param string $slug
     * @param int $id Идентификатор комментария
     * @param CommentService $commentService
     */
    public function destroy($slug, $id, CommentService $commentService)
    {
        try {
            $commentService->delete(intval($id));
        }
        catch (UnableToDeleteException|NotFoundException $e) {
            return back()->withErrors([
                'deletion_unavailable' => $e->getMessage()
            ]);
        }

        return redirect()->back();
    }
}
