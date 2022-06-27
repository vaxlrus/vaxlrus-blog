<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Http\Request;

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
        $commentId = intval($id);

        $user = User::find(auth()->id());

        if ( $user->assertIsAdmin() )
        {
            $commentService->delete($commentId, true);
        }
        else
        {
            $commentService->delete($commentId);
        }

        return back();
    }
}
